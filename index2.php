<?php
define("BAC", 2);
define("TRUNG", 1);
define("NAM", 0);

define('OPTIONAL_DAI', ['2d','3d','4d','2dai','3dai','4dai','haidai','badai','bondai']);

function cammomdump($data){
    highlight_string("<?php\n\$data =\n" . var_export($data, true) . ";\n?>");
}


function showError($mes, $opt = []){
    header('Content-Type: application/json; charset=utf-8');
    if(count($opt) == 0){
        echo json_encode(['error'=>1, 'message'=>$mes]); die;
    }else{
        echo json_encode(['error'=>1, 'message'=>$mes, 'opt'=>$opt]); die;
    }

}

function str_replace_first($search, $replace, $subject)
{
    $search = '/'.preg_quote($search, '/').'/';
    return preg_replace($search, $replace, $subject, 1);
}


function printCombination($arr,
                          $n, $r)
{
    $data = array();
    combinationUtil($arr, $data, 0,
        $n - 1, 0, $r);
}


function combinationUtil($arr, $data, $start,
                         $end, $index, $r)

{
    // Current combination is ready
    // to be printed, print it
    if ($index == $r)
    {
        for ($j = 0; $j < $r; $j++){
            return $data[$j];
        }
    }

    // replace index with all
    // possible elements. The
    // condition "end-i+1 >=
    // r-index" makes sure that
    // including one element at
    // index will make a combination
    // with remaining elements at
    // remaining positions
    for ($i = $start;
         $i <= $end &&
         $end - $i + 1 >= $r - $index; $i++)
    {
        $data[$index] = $arr[$i];
        combinationUtil($arr, $data, $i + 1,
            $end, $index + 1, $r);
    }
}

function showSuccess($mes, $opt = []){
    header('Content-Type: application/json; charset=utf-8');
    if(count($opt) == 0){
        echo json_encode(['error'=>0, 'data'=>$mes]); die;
    }else{
        echo json_encode(['error'=>0, 'data'=>$mes, 'opt'=>$opt]); die;
    }
}

function phanTichCuPhap(string $dai, string &$input, array $array_cacDai, int $indexDai): string
{
    $next = $array_cacDai[$indexDai + 1] ?? "";
    $case = null;// phân ra 2 trường hợp
    if (!empty($next)) {
        $case = 1;
        $query = "/($dai ?(.*?)) ($next)/";
    } else {
        $case = 2;
        $query = "/$dai ?(.+)/"; // đài cuối
    }
    preg_match_all($query, $input, $matches);

    if($case == 1){
        $input2 = str_replace($matches[1][0],"", $input);
        $input = $input2;
        return $matches[2][0] ?? "";
    }else{
        return $matches[1][0] ?? "";
    }

}


class GrammarLesson {
    public $apiData;
    public $dataDai;
    public $dataCachDanh;
    public $dataAllDai;
    public $inputtype; // đài bắc/ trung/ nam
    public $haveN = false;
    public $input;
    public function __construct()
    {
        if(isset($_GET['haveN']) && $_GET['haveN'] == 1){
            $this->haveN = true;
        }
        $this->getApiData();
    }

    private function getApiData(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ayeshop.com/mobile.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'ver'=> '1.0', 'app_key'=>'MANTEK@150100',
                'op'=>'mobile',
                'act'=>'apilotterytest',
                'type'=> $_GET['type'] ?? 0,
                'plus'=>'list_dai',
                'date' => $_GET['date'] ?? ""
            ],
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data =  json_decode($response);
//        cammomdump($data);
        $this->dataDai = $data->data->tendai;
        $this->dataCachDanh = $data->data->cachchoi;
        $this->dataAllDai = $data->data->alldai;
    }

    private function danhSachDaiHomNay(){
        $data = $this->dataDai;
        $result = [];
        foreach($data as $item_array){
            foreach($item_array as $items){
                foreach($items as $dai){
                    $result[] = $dai;
                }
            }
        }

        if($this->inputtype == BAC){
            $result[] = 'mienbac';
        }else{
            foreach(OPTIONAL_DAI as $dai){
                $result[] = $dai;
            }
//            $result[] = '2d';
//            $result[] = '3d';
//            $result[] = '4d';
        }
        return $result;
    }

    private function danhSachAllDai(){
        $data = $this->dataAllDai;
        $result = [];
        foreach($data as $item_array){
            foreach($item_array as $items){
                foreach($items as $dai){
                    $result[] = $dai;
                }
            }
        }

        if($this->inputtype == BAC){
            $result[] = 'mienbac';
        }else{
            foreach(OPTIONAL_DAI as $dai){
                $result[] = $dai;
            }
//            $result[] = '2d';
//            $result[] = '3d';
//            $result[] = '4d';
        }
        return $result;
    }

    private function danhSachAllCachChoi(){
        $data = $this->dataCachDanh;
        $results=[];
        foreach($data as $value){
            foreach($value as $items){
                foreach($items as $_items){
                    foreach($_items as $__items){
                        foreach($__items as $item){
                            $results[] = $item;
                        }
                    }

                }

            }
        }
        return $results;
    }

    private  function getTypeBySoDanh($sodanh){
        $data = $this->dataCachDanh;
        $results = [];
        foreach($data as $value){
            foreach($value as $key => $items){
                if($sodanh == $key){
                    foreach($items as $_items){
                        foreach($_items as $__items){
                            foreach($__items as $item){
                                $results[] = $item;
                            }

                        }

                    }
                }

            }
        }
        return $results;
    }

    private function layCachChoi($cach_choi_short){

        $data = $this->dataCachDanh;
        $result = null;
        foreach($data as $item_array){
            foreach($item_array as $ten_cach => $items){
                foreach($items as $item){
                    foreach($item as $key=>$it){
                        foreach($it as $_k =>  $i){
                            if($i === $cach_choi_short){
                                $result = $key;
                            }
                        }

                    }

                }
            }
        }

        return $result;
    }

    /*
    2d 3d 4d
    */
    private function getNDai(){
        $data = $this->dataDai;
        $result = [];
        foreach($data as $item_array){
            foreach($item_array as $key=>$items){
                foreach($items as $key => $dai){
                    if($key==0){
                        $result[] = $dai;
                    }

                }
            }
        }
        return $result;
    }

    private function getTenDai($ten_viet_tat){
        $data = $this->dataAllDai;
        $result = null;
        if(is_array($ten_viet_tat)){
            $result = [];
            foreach($ten_viet_tat as $short){
                foreach($data as $item_array){
                    foreach($item_array as $ten_dai => $items){
                        foreach($items as $dai){
                            if($dai === $short){
                                $result[] = $ten_dai;
                            }
                        }
                    }
                }
            }

            $result = implode(" ", $result);

        }else{
            foreach($data as $item_array){
                foreach($item_array as $ten_dai => $items){
                    foreach($items as $dai){
                        if($dai === $ten_viet_tat){
                            $result = $ten_dai;
                        }
                    }
                }
            }
        }


        return $result;
    }

    private function getMessageWhenError(string $body, array $cuphap)
    {
        $messages = [];
//      '/((\d+)+ ?)+? ([a-z]+) (\d+)/';
        $query_thieu_sodanh = '/[a-z]+ \d+/'; // cú pháp thiếu phần 1
        preg_match_all($query_thieu_sodanh, $body, $matches_sodanh);
        if (!empty($matches_sodanh[0])) {
            $messages[] = "Thiếu số đánh";
        }

        $query_thieu_cachdanh = '/\d+? \d+/'; // cú pháp thiếu phần 1
        preg_match_all($query_thieu_cachdanh, $body, $matches_cachdanh);
        if (!empty($matches_cachdanh[0])) {
            $messages[] = "Thiếu cách đánh";
        }

        $query_thieu_tiendanh = '/\d+? [a-z]+/'; // cú pháp thiếu phần 1
        preg_match_all($query_thieu_tiendanh, $body, $matches_tiendanh);
        if (!empty($matches_tiendanh[0])) {
            $messages[] = "Thiếu tiền đánh";
        }

        if (count($messages) == 0) {
            return "Cú pháp phần đánh của đài {$cuphap['dai']} bị sai";
        }
        return implode(", ", $messages);
    }

    private function timCachDanhBiTrung($array, $cuphap)
    {
        $tmpl = [];
        foreach($array as &$item){
            $item = $this->layCachChoi($item);
        }

        $els = ( array_unique( array_diff_assoc( $array, array_unique($array))));
        if(count($els) > 0){
//            showError("Có cách đánh bị trùng", ['highlight'=> $cuphap['body'], 'duplicate'=> $els]);
////            die;
        }
    }




    private function phantichCachDanh2($cuphap){

        //    cammomdump($cuphap);
        /*
            VT 22 33 bao 44 bay 55 66 dax 77 dc 88
            -> d: 	VT 22 bao 44
                    VT 33 bao 44
                    VT 22 bay 55
                    VT 33 bay 55
                    VT 66 dax 77
                    VT 66 dc  88

            d: VT
            cd: bao, bay, dax, dc => lấy các chữ cái non digit. Lọc thêm chữ đó có phải dạng số kéo thì bỏ qua. Còn nếu không nằm trong bộ
            type_string(bao,lo,dax,v.v..) thì -> lỗi cách đánh: XXX không tồn tại.
            -> bao, bay, dax, dc : kiểm tra đài VT có cách đánh này không?
            bao: { tien: 44, cd: bao, so: 22 33} -> str : bay 55 66 dax 77 dc 88
            bay: { tien: 55, cd: bay, so: (NaN)=>22,33 } -> so (NaN) -> 22, 33 -> str: 66 dax 77 dc 88
            dax: { tien: 77, cd: dax, so: 66} => str: dc 88
            dc : { tien: 88, cd: dc, so: NaN=> 66} => str: null
        */
        $dai = $cuphap['dai'];
        $body = $cuphap['body'];
        if($this->haveN == true){ // Có n
            // trước hết kiểm tra xem có số đánh+N không.
            $parternn = '/\d{1,}n$/';
            preg_match_all($parternn, $body, $soDanhWithN);
            if(!isset($soDanhWithN[0][0]) || empty($soDanhWithN[0][0])){
                showError("Không tìm thấy cú pháp tiền+n", ['highlight'=> ($cuphap['origin_dai'] ?? $cuphap['dai']) . "". $cuphap['body']]);
                die;
            }
        }
        $query_ky_tu_non_digit = '/([^\d ]{1,}|\d{1,}(k|khc|kht|kc|kl|khn)\d{1,}|\d{1,}n)|\d{1,}\.\d{1,}n|\d{1,}\.\d{1,}/'; // tìm các ký tự không phải là số trong chuỗi.

        preg_match_all($query_ky_tu_non_digit, $body, $ky_tu_non_digit);
        $this->kiemTraCachDanhHopLe($ky_tu_non_digit[0]); // bắt lỗi cách đánh không hợp lệ.
        $data = [];
        if(count($ky_tu_non_digit[0]) <= 0){
            showError("Không tìm thấy cách đánh trong văn bản", ['highlight'=> ($cuphap['origin_dai'] ?? $cuphap['dai']) ." ". $cuphap['body']]);
            die;
        }

        foreach($ky_tu_non_digit[0] as $_index => $_cach_danh){
            $_data_phan_tich_sodanh = $this->phanTichSoDanhDuaTrenCachDanh($_cach_danh, $body, $_index);
            $__data = [];
            foreach($_data_phan_tich_sodanh as $_item){
                $_data_item = [
                    'dai'    => $dai,
                    'sodanh' => $_item['sodanh'],
                    'cachdanh'=>$_item['cachdanh'],
                    'tien'=> $_item['tien'],
                    'index' => $_item['index'],
                ];


                if($_data_item['sodanh'] == null){
                    // lấy số đánh của index gần nhất khác null
                    $max_index = $_data_item['index'];
                    $so_danh_gan_nhat = [];
                    $index_gan_nhat = -1;
                    for($i = $max_index; $i >= 0; $i--){
                        foreach($data as $__item){
                            if($__item['index'] == $i && $__item['sodanh'] != null && ($index_gan_nhat < 0 || $index_gan_nhat == $i)){
                                $index_gan_nhat = $i;
                                break;
                            }
                        }
                    }
                    foreach($data as $__item){
                        if($__item['index'] == $index_gan_nhat){
                            $so_danh_gan_nhat[] = $__item['sodanh'];

                        }
                    }


                    if(count($so_danh_gan_nhat) == 0){
                        showError("Không tìm được số đánh cho cách đánh [$_cach_danh]",['highlight'=>$body]);
                        die;
                    }else{
                        $so_danh_gan_nhat = array_unique($so_danh_gan_nhat, SORT_REGULAR);
                    }

                    foreach($so_danh_gan_nhat as $__so_danh_gan_nhat_single){
                        if( !is_array($__so_danh_gan_nhat_single )){
                            $__so_danh_gan_nhat_single_data = [
                                'dai'    => $dai,
                                'sodanh' => $__so_danh_gan_nhat_single,
                                'cachdanh'=>$_item['cachdanh'],
                                'tien'=>   $_item['tien'],
                                'index' => $_item['index'],
                                'keydai'=> $this->getTenDai($dai),
                                'keydanh'=>$this->layCachChoi($_item['cachdanh'])

                            ];
                            $__data[] = $__so_danh_gan_nhat_single_data;
                        }else{
                            foreach($__so_danh_gan_nhat_single as $__single){
                                $__data[] = [
                                    'dai'    => $dai,
                                    'sodanh' => $__single,
                                    'cachdanh'=>$_item['cachdanh'],
                                    'tien'=> $_item['tien'],
                                    'index' => $_item['index'],
                                    'keydai'=> $this->getTenDai($dai),
                                    'keydanh'=>$this->layCachChoi($_item['cachdanh'])

                                ];
                            }
                        }
                    }
                }else{
                    $__data[] = $_data_item;
                }
            }
            $full_string_cachchoi = ($this->layCachChoi($_cach_danh));
            if($full_string_cachchoi === 'dathang'){
                $__data = $this->tachDaThang($__data);
            }elseif($full_string_cachchoi === 'daxien'){
                $__data = $this->tachDaXien($__data);
            }else{
                $__data = $this->tachBinhThuong($__data);
            }
            foreach($__data as $d){
                $data[] = $d;
            }

        }

        $tmpl = $data;
        $_compare_tmpl = [];

        foreach($tmpl as &$_tmpl){
            unset($_tmpl['index']);
            unset($_tmpl['tien']);
            if(in_array($_tmpl, $_compare_tmpl)){
                $this->timCachDanhBiTrung($_tmpl, $cuphap);
//                showError("Có cách đánh bị trùng ! " , ['highlight'=> $_tmpl]);
//                die;
            }
            $_compare_tmpl[] = $_tmpl;
        }




        return $data;
        // $data = $this->phanTichSoDanhDuaTrenCachDanh($start_index_cach_danh,$ky_tu_non_digit[0], $body);



    }

    private function kiemTraCachDanhHopLe(&$cach_danh){
        $tat_ca_cachdanh = $this->danhSachAllCachChoi(); // lấy danh sách tất cả các cách đánh
        // kiểm tra xem có phải là số kéo không thì cũng bỏ qua.
        $query_so_keo = '/(\d{1,}(k|khc|kht|kc|kl|khn)\d{1,})/';
        $query_so_danh_n = '/\d{1,}n/';
        $partern_tien_thap_phan = "/\d{1,}\.\d{1,}/";

        foreach($cach_danh as $index=>$word){
            preg_match_all($query_so_keo, $word, $matches_sokeo);
            preg_match_all($query_so_danh_n, $word, $matches_so_co_n);
            preg_match($partern_tien_thap_phan, $word, $mmm);
            if(!empty($mmm)){
                unset($cach_danh[$index]);
                continue;
            }
            if(!empty($matches_so_co_n[0][0])){
                // bỏ N
                $word  = preg_replace(["/(\d{1,})(n)/"], "$1", $word);
                unset($cach_danh[$index]);
                continue;

            }else{
                if($this->haveN && !in_array($word, $tat_ca_cachdanh)){
                    $pattern_errors = "/.*?(";

                    $pattern_errors .= ($cach_danh[$index-1]??"").".*?";
                    $pattern_errors .= $cach_danh[$index];
                    $pattern_errors .= ".*?" .$cach_danh[$index+1];
                    $pattern_errors .= ")/";
                    preg_match($pattern_errors, $this->input, $___e_m);
//                    cammomdump($pattern_errors);
//                    cammomdump($___e_m);
                    showError("cách đánh [$word] không hợp lệ", ['highlight'=> $___e_m[0],'a'=>$cach_danh]);
                    die;
                }
            }

            if(!in_array($word, $tat_ca_cachdanh) && empty($matches_sokeo[0][0])){
                showError("Cách đánh [$word] không tồn tại",['highlight'=>$word, 'avaiable'=> $tat_ca_cachdanh,'mmm'=>$mmm]);
                die;
            }

            if(!empty($matches_sokeo[0][0])){
                // bỏ số kéo ra khỏi cách đánh
                unset($cach_danh[$index]);
            }
        }

    }

    private function phanTichSoDanhDuaTrenCachDanh($cach_danh, &$body_string, $index)
    {
        $start_array = (explode($cach_danh, $body_string));
        $start_string = $start_array[0];

//        $query_so_danh = "/((.+)? ?($cach_danh)) ?(\d+){1,1}/"; // lấy các số đứng trước $cach_danh
        $query_so_danh = "/(($start_string)($cach_danh)) ?(\d{1,}\.\d{1,}|(\d+){1,1})/"; // lấy các số đứng trước $cach_danh
        preg_match_all($query_so_danh, $body_string, $matches_so_danh);
//        cammomdump($matches_so_danh);
        $tiendanh = $matches_so_danh[4][0] ?? "";
        if($this->haveN){
            $parten_validate_tiendanh = '/'.$tiendanh.'n/';
            preg_match_all($parten_validate_tiendanh, $body_string, $matches_validate);
            if(empty($matches_validate[0][0])){
                showError("Tiền đánh sai cấu trúc số+n",['highlight'=>$tiendanh,'cachdanh'=>$cach_danh]);
                die;
            }
        }
        $body_string  = preg_replace(["/($tiendanh)(n)/"], "$1", $body_string, 1);

        if(empty($tiendanh)){
            showError("Không xác định được tiền đánh trong cách đánh [$cach_danh]", ['highlight'=>$cach_danh]);
            die;
        }
        $explode = explode(".", $tiendanh);
        if(count($explode) > 1){
            $explode = explode(".", $tiendanh);
            if(strlen($explode[1]) >= 2){
                showError("Tiền đánh float chưa đúng", ['highlight'=>$tiendanh]);
                die;
            }

        }
        $so_danh = $matches_so_danh[2][0];
        $body_string = str_replace_first($matches_so_danh[0][0],"", $body_string); // bỏ phần đã tìm được ra khỏi body
        $so_danh = trim($so_danh);
        $so_danh_array = explode(" ", $so_danh);
        $result = [];
        foreach($so_danh_array as $item){
            $_sodanh = trim($item);
            $_sodanh = empty($_sodanh) ? null : $_sodanh;
            $result[] = [
                'cachdanh'=>$cach_danh,
                'sodanh' => $_sodanh,
                'tien' => $tiendanh,
                'index' => $index,
            ];
        }
        return $result;
    }

    private function kiemtraDauDuoi(&$input){
        $parrtern = '/d(\d{1,}\.\d{1,}|\d{1,})/'; // kiểm tra cú pháp d+số
        if($this->haveN){
            $parrtern = '/d(\d{1,}\.\d{1,}|\d{1,})n/'; // kiểm tra cú pháp d+số
        }
        preg_match_all($parrtern, $input, $matches);
        if(!empty($matches[0][0])){
            $firstMatch_group = $matches[0];
            foreach($firstMatch_group as $key=>$match_item){
                if(!empty($matches[1][$key])){
                    $number1 = $matches[1][$key];
//                    if(strlen($number1) != 2){
//                        showError("d phải kèm 1 số 2 chữ số", ['highlight'=> $matches[0][$key]]);
//                        die;
//                    }

                    $parten2 = "/".$matches[0][$key]."n? ?(d\d{1,}\.\d{1,}|d\d{1,})n?/";
                    if($this->haveN){
                        $parten2 = "/".$matches[0][$key]." ?(d\d{1,}\.\d{1,}n|d\d{1,}n)/";
                    }
                    preg_match_all($parten2, $input, $matches2);
                    if(isset($matches2[0][0])){
                        $matches2_item = $matches2[0][0];
                        $matches2_item2 = $matches2[1][0];
                        $new_partern = "/(.*?)(".$matches2_item.")/";
                        preg_match($new_partern, $input, $__matches);
                        $_need_replace = $__matches[2];

                        if(!empty($matches2_item)){
                            $_clone_need_replace = $_need_replace;
                            $_need_replace = str_replace_first($matches[0][$key], str_replace("d","dau", $matches[0][$key]), $_need_replace);
                            $_need_replace = str_replace_first($matches2_item2, str_replace("d","duoi", $matches2_item2), $_need_replace);
//                            $input = str_replace_first($matches[0][$key], str_replace("d","dau", $matches[0][$key]), $input);
//                            $input = str_replace_first($matches2_item2, str_replace("d","duoi", $matches2_item2), $input);
//                            cammomdump($_clone_need_replace);
//                            cammomdump($_need_replace);
                            $input = str_replace_first($_clone_need_replace, $_need_replace, $input);

                        }
                    }


                }
            }

        }

    }

    private function converOptinalDai(&$dai, &$input){
        $dai_new = str_replace("dai","d", $dai);
        $dai_new = str_replace(['hai','ba','bon'],['2','3','4'], $dai_new);
        $input = str_replace($dai, $dai_new, $input);
        $dai = $dai_new;
        $dai = trim($dai);
//        cammomdump($input);
    }

    /*
    // phần này sẽ tách cả chuỗi input ra thành từng bộ phận sau đó mới tới các step sau.
    */
    private function TachCuPhap($input){
        // step1: (đài{1,})(số-đánh{1,}|số-kéo)(cách-đánh)(tiền-đánh{1})
        $all_dai = $this->danhSachAllDai();
        $str_all_dai = implode("|", $all_dai);
        $queryGetDai = "/((($str_all_dai) ?)+) ??(\d+)/";
        if(isset($_GET['type']) && $_GET['type'] == 1){
            //(((?<!\d)(kh) ?)+) ??(\d+)?
            $queryGetDai = "/(((?<!\d)($str_all_dai) ?)+) ??(\d+)/";
        }
//         cammomdump($queryGetDai);
        preg_match_all($queryGetDai, $input, $matches_dai);
//        cammomdump($all_dai);
        if(!isset($matches_dai[1]) or (isset($matches_dai[1]) && empty($matches_dai[1]))){
            showError("Không tìm thấy cú pháp đài+số đánh", ['highlight'=>$input]);
            die;
        }
        $dai_da_tim_thay = $matches_dai[1];
        // cammomdump($matches_dai[1]);
        // chia các đài ra các mảng củ pháp.
        $this->kiemtraDauDuoi($input);
//        cammomdump($input);
        $cac_cu_phap = [];
        $this->ConvertOptinalArrayDai($dai_da_tim_thay, $input);
        foreach($dai_da_tim_thay as $indexDai => $dai){
            if(in_array(trim($dai),OPTIONAL_DAI)){
                // lấy ra N đài đầu tiên.
                $this->converOptinalDai($dai, $input);
                $dai = trim($dai);
                $number = str_replace("d", "", $dai);
                $ndai = $this->getNDai();
                $_str_n_dai = [];
                for($i=0;$i<$number;$i++){
                    if(!isset($ndai[$i])){
                        showError("Hôm nay chỉ có ". (count($this->getNDai())) ." đài" , ['d'=> $this->getNDai()]);
                        die;
                    }
                    $_str_n_dai[] = $ndai[$i];

                }
                $cac_cu_phap[] = [
                    'origin_dai'=>$dai,
                    'dai'  => implode(" ", $_str_n_dai),
                    'body' => trim(phanTichCuPhap($dai, $input, $dai_da_tim_thay, $indexDai))
                ];

            }else{

                $cac_cu_phap[] = [
                    'dai'  => $dai,
                    'body' => trim(phanTichCuPhap($dai, $input, $dai_da_tim_thay, $indexDai))
                ];
            }

        }
        $data = [];
        // step2: lấy ra các cách chơi từ cú pháp bên trên.
        foreach ($cac_cu_phap as &$_cp) {
            $data[] = $this->phantichCachDanh2($_cp);
        }

        // cammomdump($data);

        return $data;
    }

    private function ConvertOptinalArrayDai(&$dai_da_tim_thay, &$input){
        $_new_dai_da_tim_thay = [];
        foreach($dai_da_tim_thay as $dai){
            $this->converOptinalDai($dai, $input);
            $dai = trim($dai);
            $_new_dai_da_tim_thay[] = $dai;
        }

        $dai_da_tim_thay = $_new_dai_da_tim_thay;
    }

    /*
        Check xem hôm nay có đài này không
        return boolean.
    */
    private function checkDaiHomNay($ten_viet_tat){
        if($this->inputtype == BAC){
            return true;// bỏ qua check đài miền bắc
        }
        $ten_viet_tat = trim($ten_viet_tat);
        $daiHomnay = $this->dataDai;
        foreach($daiHomnay as $keyDai => $array_dai){
            foreach($array_dai as $_arr_dai){
                foreach($_arr_dai as $dai){
                    if($dai === $ten_viet_tat){
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /*
        Kiểm tra số đánh có phải là số kéo không.
    */
    private function phanTichSoKeo($_normalItem, $_dai){

        $sodanh = $_normalItem['sodanh'];
        $query = "/(((\d{1,})(k|khc|kht|kc|kl|khn)(\d{1,})))/";
        preg_match_all($query, $sodanh, $matches);
        if(!empty($matches[1][0])){
            $loai_keo = trim($matches[4][0]);
            $min = trim($matches[3][0]);
            $max = trim($matches[5][0]);
            $padnum = 2;

            switch($loai_keo){
                case "k":
                    $padnum = 2;
                    break;
                case "khc":
                    $padnum = 2;
                    break;
                case "kht":
                    $padnum = 3;
                    break;
                case "khn":
                    $padnum = 4;
                    break;
                case "kl":
                    $padnum = 2;
                    break;
                case "kc":
                    $padnum = 2;
                    break;
            }
            $min = str_pad($min,$padnum, "0", STR_PAD_LEFT);
            $max = str_pad($max,$padnum, "0", STR_PAD_LEFT);
            $str_len_min  = strlen($min);
            $str_len_max = strlen($max);
            $cachdanh = $_normalItem['cachdanh'];
            $array_data = ['daudao','duoidao','dauduoidao','baylodao','baolodao'];
            if(in_array($this->layCachChoi($cachdanh), $array_data)){
                if($str_len_min < 3){
                    showError("Số Kéo $min -> $max không thể ít hơn 3 chữ số", ['highlight'=> $sodanh]);
                    die;
                }
            }
            if((int)$max <= (int)$min){
                showError("Số Kéo $max không thể nhỏ hơn hoặc bằng số kéo $min", ['highlight'=> $sodanh]);
                die;
            }
            if($str_len_min != $str_len_max){
                showError("Số kéo $min($str_len_min con) và $max($str_len_max con) không cùng loại:", ['highlight'=> $sodanh]);
                die;
            }

            if($str_len_min >= 5){
                showError("Tối đa được phép kéo hàng nghìn", ['highlight'=> $sodanh]);
                die;
            }

            $sokeo_type = $str_len_max."con";
            if($loai_keo == 'khc'){
                $this->sosanhkeo($min, $max, 2, $sodanh);
                if($str_len_min < 2){
                    showError("Số kéo hàng chục phải từ 2 số trở lên", ['highlight'=> "{$min} và {$max}"]);
                    die;
                }
                if($min[strlen($min) - 1] != $max[strlen($max) - 1]){
                    showError("Số kéo hàng chục không giống nhau {$min[strlen($min) - 1]} và {$max[strlen($max) - 1]}", ['highlight'=> $sodanh,'min'=>$min,'max'=>$max]);
                    die;
                }
            }

            if($loai_keo == 'kht'){
                $this->sosanhkeo($min, $max, 3, $sodanh);

                if($str_len_min < 3){
                    showError("Số kéo hàng chục phải từ 3 số trở lên", ['highlight'=> "{$min} và {$max}"]);
                    die;
                }
                if($min[strlen($min) - 1].$min[strlen($min) - 2] != $max[strlen($max) - 1].$max[strlen($max) - 2]){
                    showError("Số kéo hàng trăm không giống nhau {$min[strlen($min) - 1]}{$min[strlen($min) - 2]} và {$max[strlen($max) - 1]}{$max[strlen($max) - 2]}", ['highlight'=> $sodanh]);
                    die;
                }
            }

            if($loai_keo == 'khn'){
                $this->sosanhkeo($min, $max, 4, $sodanh);

                if($str_len_min < 4){
                    showError("Số kéo hàng nghìn phải từ 4 số trở lên", ['highlight'=> "{$min} và {$max}"]);
                    die;
                }
                if($min[strlen($min) - 1].$min[strlen($min) - 2].$min[strlen($min) - 3] != $max[strlen($max) - 1].$max[strlen($max) - 2].$max[strlen($max) - 3]){
                    showError("Số kéo hàng nghìn không giống nhau", ['highlight'=> $sodanh]);
                    die;
                }
            }

            if($loai_keo == 'kl'){
                if($min % 2 == 0 || $max % 2 == 0){

                    // showError("Số kéo Lẻ phải là số lẻ.", ['highlight'=> [$min, $max]]);
                    // die;

                }
            }

            if($loai_keo == 'kc'){
                if($min % 2 != 0 || $max % 2 != 0){

                    // showError("Số kéo chẵn phải là số chẵn.", ['highlight'=> [$min, $max]]);
                    // die;

                }
            }

            $result = [];
            $increment_num = 0;
            switch($loai_keo){
                case "k":
                    $increment_num = 1;
                    break;
                case "khc":
                    $increment_num = 10;
                    break;
                case "kht":
                    $increment_num = 100;
                    break;
                case "khn":
                    $increment_num = 1000;
                    break;
                case "kl":
                    $increment_num = 1;
                    break;
                case "kc":
                    $increment_num = 1;
                    break;
            }

            for($i=$min; $i<=$max;$i+=$increment_num){
                if($loai_keo == 'kl'){
                    if($i % 2 == 0){
                        $i ++;
                    }
                }
                if($loai_keo == 'kc'){
                    if($i % 2 != 0){
                        $i ++;
                    }
                }
                if($i>=$min && $i<=$max){
                    $result[] = [
                        'dai'     => $_dai,
                        'cachdanh'=> $_normalItem['cachdanh'],
                        'sodanh'  => str_pad($i, $str_len_max, "0", STR_PAD_LEFT),
                        'tien'    => $_normalItem['tien'],
                        'index'   => $_normalItem['index'],
                        'keydai'=> $this->getTenDai($_dai),
                        'keydanh'=>$this->layCachChoi($_normalItem['cachdanh'])
                    ];
                }

            }


            return $result;
        }
        return false;
    }


    private function sosanhkeo($min, $max, $reverse_index, $sodanh){
        $split_min = str_split($min);
        $split_max = str_split($max);
        $index = count($split_min) - $reverse_index;
        foreach ($split_min as $min_index=>$min_value){
            foreach($split_max as $max_index=>$max_value){
                if($min_index != $index && $min_index == $max_index && $min_value != $max_value){
//                    cammomdump($min_value);
//                    cammomdump($max_value);
                    showError("Số kéo sai cú pháp.", ['highlight'=> $sodanh]); die;
                }
            }
        }
    }


    /*
    Tách số kéo từ cú pháp đã tách sơ bộ ở @TachCuPhap()
    00k10 -> kéo từ 00, 01 -> 10
    khc -> kéo hàng chục: 11k31 -> 11 21 31
    */
    private function tachSoKeo(){

    }

    private function tachBinhThuong($data){
        // kiểm tra đài.
        $result = [];
        foreach($data as $_normalItem){
            $dai = explode(" ", trim($_normalItem['dai']));
            foreach($dai as $_dai){
                $check_dai_hom_nay = $this->checkDaiHomNay($_dai);
                if($check_dai_hom_nay == false){
                    showError("-Ngày hôm nay không có đài [$_dai]", ['highlight'=> $_dai]);
                    die;
                }


                $data_sokeo = $this->phanTichSoKeo($_normalItem, $_dai);
                if($data_sokeo == false){
                    $strlen  = strlen($_normalItem['sodanh']);
                    if($strlen <= 1 || $strlen >= 5){
                        showError("Số đánh phải là 1 số từ 2-4 chữ số" , ['highlight'=> $_normalItem['sodanh']]);
                        die;
                    }
                    $types = $this->getTypeBySoDanh($strlen."con");
                    if(in_array($_normalItem['cachdanh'], $types) == false){
                        showError("cách đánh $strlen con không thể đánh {$_normalItem['cachdanh']}");
                        die;
                    }
                    // trường hợp số thường, không phải số kéo.
                    $result[] = [
                        'dai'     => $_dai,
                        'sodanh'  => $_normalItem['sodanh'],
                        'cachdanh'=> $_normalItem['cachdanh'],
                        'tien'    => $_normalItem['tien'],
                        'index'   => $_normalItem['index'],
                        'keydai'=> $this->getTenDai($_dai),
                        'keydanh'=>$this->layCachChoi($_normalItem['cachdanh'])

                    ];
                }else{
                    foreach($data_sokeo as $_sokeo){
                        $result[] = $_sokeo;
                    }
                }

            }
        }
        return $result;
    }

    private function ketHopSoDao($items, &$results, $perms = array()){
        if (empty($items)) {
            $results[] = implode("", $perms);
//            print join(' ', $perms) . "\n";
        }  else {
            for ($i = count($items) - 1; $i >= 0; --$i) {
                $newitems = $items;
                $newperms = $perms;
                list($foo) = array_splice($newitems, $i, 1);
                array_unshift($newperms, $foo);
                $this->ketHopSoDao($newitems, $results, $newperms);
            }
        }

        return $results;
    }

    private function tachSodao(&$data){
        $array_data = ['daudao','duoidao','dauduoidao','baylodao','baolodao'];
        foreach($data as &$cp){
            foreach ($cp as $item){
                $keydanh = $item['keydanh'];
                if(in_array($keydanh, $array_data)){
                    // là type đảo thì đảo.
                    $sodanh_array = str_split($item['sodanh']);
                    $results = [];
                    $this->ketHopSoDao($sodanh_array, $results);
                    $results = array_unique($results, SORT_REGULAR);
                    foreach($results as $_item){
                        if($_item != $item['sodanh']){
                            $clone_item = $item;
                            $clone_item['sodanh'] = $_item;
                            $cp[] = $clone_item;
                        }
                    }
                }
            }

        }

        return $data;
    }

    /*
        đánh 2->4 đài, đánh từ 2 số trở lên : longan hcm  20 30 40 dax 30 -> longan 20 30 dax 30, longan 20 40 dax 30, long an 30 40 dax 30 ( hcm tương tự )
    */
    private function tachDaXien($data){
        // kiểm tra xem số đài.
        $first_data = $data[0];
        $dai  = explode(" ", trim($first_data['dai']));
        foreach($dai as $_dai){
            $_dai = trim($_dai);
            if(!empty($_dai)){
                $check_dai_hom_nay = $this->checkDaiHomNay($_dai);
                if($check_dai_hom_nay == false){
                    showError("--Ngày hôm nay không có đài [$_dai]", ['highlight'=> $_dai]);
                    die;
                }
            }

        }
        $so_dai = count($dai);
        if($so_dai < 2){
            if($this->inputtype != BAC){
                showError("Số đài trong đá xiên phải từ 2 trở lên",['highlight'=> $dai]);
                die;
            }
        }
        $count_sodanh = count($data);
        $arr_sodanh = [];
        if($count_sodanh < 2){
            showError("cách chơi đá xiên phải có 2 số trở lên",['highlight'=> $data[0]['sodanh']]);
            die;
        }
        foreach($data as $daxien_item){
            $arr_sodanh[] = $daxien_item['sodanh'];
            if(strlen($daxien_item['sodanh']) != 2){
                showError("Số trong cách chơi đá xiên phải là số có 2 chữ số", ['highlight'=> $daxien_item['sodanh']]);
                die;
            }
        }
        $kethopso = [];
        foreach($arr_sodanh as $k1=>$sd){
            foreach($arr_sodanh as $k2=>$sd2){
                if($k1 !== $k2){
                    $_sd_data = [$sd, $sd2];
                    sort($_sd_data);
                    $kethopso[] = $_sd_data;
                }
            }
        }
        $result = [];
        $ket_hop_so = array_unique($kethopso, SORT_REGULAR);
        foreach($ket_hop_so as $so){
            $result[] = [
                'dai'=>implode(" ",$dai),
                'cachdanh'=> $first_data['cachdanh'],
                'sodanh'  => $so,
                'tien'    => $first_data['tien'],
                'index'   => $first_data['index'],
                'keydai' => $this->getTenDai($dai),
                'keydanh'=>$this->layCachChoi($first_data['cachdanh'])


            ];
        }

        return $result;
    }

    /*
        phải đánh 2 con số trở lên, 2 con số tương ứng với 1 lệnh, check lại tổ hợp,chỉ dc đánh số có 2 chữ số
    */
    private function tachDaThang($data){

        $count_sodanh = count($data);
        $arr_sodanh = [];
        if($count_sodanh < 2){
            showError("cách chơi đá thẳng phải có 2 số trở lên", ['highlight'=>$data[0]['sodanh']]);
            die;
        }
        foreach($data as $dathang_item){
            $arr_sodanh[] = $dathang_item['sodanh'];
            if(strlen($dathang_item['sodanh']) != 2){
                showError("Số trong cách chơi đá thẳng phải là số có 2 chữ số", ['highlight'=> $dathang_item['sodanh']]);
                die;
            }
        }

        $kethopso = [];
        foreach($arr_sodanh as $k1=>$sd){
            foreach($arr_sodanh as $k2=>$sd2){
                if($k1 !== $k2){
                    $_sd_data = [$sd, $sd2];
                    sort($_sd_data);
                    $kethopso[] = $_sd_data;
                }
            }
        }

        $result = [];
        $ket_hop_so = array_unique($kethopso, SORT_REGULAR);
        $array_dai = explode(" ", trim($data[0]['dai']));
        foreach($array_dai as $__dai){
            $check_dai_hom_nay = $this->checkDaiHomNay(trim($__dai));
            if($check_dai_hom_nay == false){
                showError("[3]Ngày hôm nay không có đài [{$__dai}]", ['highlight'=> $__dai]);
                die;
            }

            foreach($ket_hop_so as $so){
                $result[] = [
                    'dai' =>$__dai,
                    'cachdanh'=>$data[0]['cachdanh'],
                    'sodanh' => $so,
                    'tien' => $data[0]['tien'],
                    'index' => $data[0]['index'],
                    'keydanh'=>$this->layCachChoi($data[0]['cachdanh'])

                ];
            }
        }




        return $result;
    }

    private function soDanhArrayToString(&$cuphap_da_tach){
        foreach($cuphap_da_tach as &$cuphapNho){
            foreach($cuphapNho as &$item){
                $item['sodanh'] = is_array($item['sodanh']) ? implode(" ", $item['sodanh']) : $item['sodanh'];
            }
        }
    }

    public function verify(){
        $input = $_GET['s'];
        $input = preg_replace('/\s\s+/', ' ', $input); // replace các khoảng trắng liên tục về khoảng trắng duy nhất
        $this->inputtype = $_GET['type'] ?? null;
        if($this->inputtype != null && $this->inputtype == BAC){
            $input = "mienbac ".$input;
        }
        $this->input = $input;
        $cuphap_da_tach = $this->TachCuPhap($input);
        $this->soDanhArrayToString($cuphap_da_tach);
        $this->tachSodao($cuphap_da_tach);
        foreach($cuphap_da_tach as &$cuphap){
            array_unique($cuphap, SORT_REGULAR);
        }

        showSuccess($cuphap_da_tach);
        die;

    }
}


$app = new GrammarLesson();
$app->verify();



