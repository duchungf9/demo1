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
    $case = null;// ph??n ra 2 tr?????ng h???p
    if (!empty($next)) {
        $case = 1;
        $query = "/($dai ?(.*?)) ($next)/";
    } else {
        $case = 2;
        $query = "/$dai ?(.+)/"; // ????i cu???i
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
    public $inputtype; // ????i b???c/ trung/ nam
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
//            CURLOPT_URL => 'https://ayeshop.com/mobile.php',
            CURLOPT_URL => 'http://bearshoping.com/mobile.php',
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
//        cammomdump($data->data);
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
        $query_thieu_sodanh = '/[a-z]+ \d+/'; // c?? ph??p thi???u ph???n 1
        preg_match_all($query_thieu_sodanh, $body, $matches_sodanh);
        if (!empty($matches_sodanh[0])) {
            $messages[] = "Thi???u s??? ????nh";
        }

        $query_thieu_cachdanh = '/\d+? \d+/'; // c?? ph??p thi???u ph???n 1
        preg_match_all($query_thieu_cachdanh, $body, $matches_cachdanh);
        if (!empty($matches_cachdanh[0])) {
            $messages[] = "Thi???u c??ch ????nh";
        }

        $query_thieu_tiendanh = '/\d+? [a-z]+/'; // c?? ph??p thi???u ph???n 1
        preg_match_all($query_thieu_tiendanh, $body, $matches_tiendanh);
        if (!empty($matches_tiendanh[0])) {
            $messages[] = "Thi???u ti???n ????nh";
        }

        if (count($messages) == 0) {
            return "C?? ph??p ph???n ????nh c???a ????i {$cuphap['dai']} b??? sai";
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
//            showError("C?? c??ch ????nh b??? tr??ng", ['highlight'=> $cuphap['body'], 'duplicate'=> $els]);
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
            cd: bao, bay, dax, dc => l???y c??c ch??? c??i non digit. L???c th??m ch??? ???? c?? ph???i d???ng s??? k??o th?? b??? qua. C??n n???u kh??ng n???m trong b???
            type_string(bao,lo,dax,v.v..) th?? -> l???i c??ch ????nh: XXX kh??ng t???n t???i.
            -> bao, bay, dax, dc : ki???m tra ????i VT c?? c??ch ????nh n??y kh??ng?
            bao: { tien: 44, cd: bao, so: 22 33} -> str : bay 55 66 dax 77 dc 88
            bay: { tien: 55, cd: bay, so: (NaN)=>22,33 } -> so (NaN) -> 22, 33 -> str: 66 dax 77 dc 88
            dax: { tien: 77, cd: dax, so: 66} => str: dc 88
            dc : { tien: 88, cd: dc, so: NaN=> 66} => str: null
        */
        $dai = $cuphap['dai'];
        $body = $cuphap['body'];
        if($this->haveN == true){ // C?? n
            // tr?????c h???t ki???m tra xem c?? s??? ????nh+N kh??ng.
            $parternn = '/\d{1,}n$/';
            preg_match_all($parternn, $body, $soDanhWithN);
            if(!isset($soDanhWithN[0][0]) || empty($soDanhWithN[0][0])){
                showError("Kh??ng t??m th???y c?? ph??p ti???n+n", ['highlight'=> ($cuphap['origin_dai'] ?? $cuphap['dai']) . "". $cuphap['body']]);
                die;
            }
        }
        $query_ky_tu_non_digit = '/([^\d ]{1,}|\d{1,}(k|khc|kht|kc|kl|khn)\d{1,}|\d{1,}n)|\d{1,}\.\d{1,}n|\d{1,}\.\d{1,}/'; // t??m c??c k?? t??? kh??ng ph???i l?? s??? trong chu???i.

        preg_match_all($query_ky_tu_non_digit, $body, $ky_tu_non_digit);
        $this->kiemTraCachDanhHopLe($ky_tu_non_digit[0]); // b???t l???i c??ch ????nh kh??ng h???p l???.
        $data = [];
        if(count($ky_tu_non_digit[0]) <= 0){
            showError("Kh??ng t??m th???y c??ch ????nh trong v??n b???n", ['highlight'=> ($cuphap['origin_dai'] ?? $cuphap['dai']) ." ". $cuphap['body']]);
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
                    // l???y s??? ????nh c???a index g???n nh???t kh??c null
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
                        showError("Kh??ng t??m ???????c s??? ????nh cho c??ch ????nh [$_cach_danh]",['highlight'=>$body]);
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
//                showError("C?? c??ch ????nh b??? tr??ng ! " , ['highlight'=> $_tmpl]);
//                die;
            }
            $_compare_tmpl[] = $_tmpl;
        }




        return $data;
        // $data = $this->phanTichSoDanhDuaTrenCachDanh($start_index_cach_danh,$ky_tu_non_digit[0], $body);



    }

    private function kiemTraCachDanhHopLe(&$cach_danh){
        $tat_ca_cachdanh = $this->danhSachAllCachChoi(); // l???y danh s??ch t???t c??? c??c c??ch ????nh
        // ki???m tra xem c?? ph???i l?? s??? k??o kh??ng th?? c??ng b??? qua.
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
                // b??? N
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
                    showError("c??ch ????nh [$word] kh??ng h???p l???", ['highlight'=> $___e_m[0],'a'=>$cach_danh]);
                    die;
                }
            }

            if(!in_array($word, $tat_ca_cachdanh) && empty($matches_sokeo[0][0])){
                $pattern_errors = "/.*?(";
                $pattern_errors .= ($cach_danh[$index-1]??"").".*?";
                $pattern_errors .= $cach_danh[$index];
                $pattern_errors .= ".*?" .$cach_danh[$index+1];
                $pattern_errors .= ")/";
                preg_match($pattern_errors, $this->input, $___e_m);
                showError("c??ch ????nh [$word] kh??ng h???p l???", ['highlight'=> $___e_m[0],'a'=>$cach_danh]);
                die;
            }

            if(!empty($matches_sokeo[0][0])){
                // b??? s??? k??o ra kh???i c??ch ????nh
                unset($cach_danh[$index]);
            }
        }

    }

    private function phanTichSoDanhDuaTrenCachDanh($cach_danh, &$body_string, $index)
    {
        $start_array = (explode($cach_danh, $body_string));
        $start_string = $start_array[0];

//        $query_so_danh = "/((.+)? ?($cach_danh)) ?(\d+){1,1}/"; // l???y c??c s??? ?????ng tr?????c $cach_danh
        $query_so_danh = "/(($start_string)($cach_danh)) ?(\d{1,}\.\d{1,}|(\d+){1,1})/"; // l???y c??c s??? ?????ng tr?????c $cach_danh
        preg_match_all($query_so_danh, $body_string, $matches_so_danh);
//        cammomdump($matches_so_danh);
        $tiendanh = $matches_so_danh[4][0] ?? "";
        if($this->haveN){
            $parten_validate_tiendanh = '/'.$tiendanh.'n/';
            preg_match_all($parten_validate_tiendanh, $body_string, $matches_validate);
            if(empty($matches_validate[0][0])){
                showError("Ti???n ????nh sai c???u tr??c s???+n",['highlight'=>$tiendanh,'cachdanh'=>$cach_danh]);
                die;
            }
        }
        $body_string  = preg_replace(["/($tiendanh)(n)/"], "$1", $body_string, 1);

        if(empty($tiendanh)){
            showError("Kh??ng x??c ?????nh ???????c ti???n ????nh trong c??ch ????nh [$cach_danh]", ['highlight'=>$cach_danh]);
            die;
        }
        $explode = explode(".", $tiendanh);
        if(count($explode) > 1){
            $explode = explode(".", $tiendanh);
            if(strlen($explode[1]) >= 2){
                showError("Ti???n ????nh float ch??a ????ng", ['highlight'=>$tiendanh]);
                die;
            }

        }
        $so_danh = $matches_so_danh[2][0];
        $body_string = str_replace_first($matches_so_danh[0][0],"", $body_string); // b??? ph???n ???? t??m ???????c ra kh???i body
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
        $parrtern = '/d(\d{1,}\.\d{1,}|\d{1,})/'; // ki???m tra c?? ph??p d+s???
        if($this->haveN){
            $parrtern = '/d(\d{1,}\.\d{1,}|\d{1,})n/'; // ki???m tra c?? ph??p d+s???
        }
        preg_match_all($parrtern, $input, $matches);
        if(!empty($matches[0][0])){
            $firstMatch_group = $matches[0];
            foreach($firstMatch_group as $key=>$match_item){
                if(!empty($matches[1][$key])){
                    $number1 = $matches[1][$key];
//                    if(strlen($number1) != 2){
//                        showError("d ph???i k??m 1 s??? 2 ch??? s???", ['highlight'=> $matches[0][$key]]);
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
        $dai_new = $dai;
        $dai_new = str_replace(['haidai','badai','bondai','2dai','3dai','4dai'],['2d','3d','4d','2d','3d','4d'], $dai_new);
        $input = str_replace($dai, $dai_new, $input);
        $dai = $dai_new;
        $dai = trim($dai);
//        cammomdump($input);
    }

    /*
    // ph???n n??y s??? t??ch c??? chu???i input ra th??nh t???ng b??? ph???n sau ???? m???i t???i c??c step sau.
    */
    private function TachCuPhap($input){
        $input =  preg_replace("/(d[\d]+)(d[\d]+)/", "$1 $2", $input); // t??ch d???ng d2d34 -> d2 d34

//        cammomdump($input);
        // step1: (????i{1,})(s???-????nh{1,}|s???-k??o)(c??ch-????nh)(ti???n-????nh{1})
        $all_dai = $this->danhSachAllDai();
        $clone_all_dai = $all_dai;
        foreach ($all_dai as $key=>$dai){
            if(in_array($dai,OPTIONAL_DAI)){// tr?????c 2d 3d 4d ph???i c?? space, ho???c ph???i l?? ?????u d??ng
                unset($all_dai[$key]);
                $all_dai[] = " ".$dai;
                $all_dai[] = "^".$dai;
            }
        }
        //        cammomdump($all_dai);

        $str_all_dai = implode("|", $all_dai);
        $str_clone_all_dai = implode("|", $clone_all_dai);
        // ki???m tra text ?????u ph???i l?? ????i.
        preg_match("/^($str_clone_all_dai)/",$input, $match_starting);
        if(empty($match_starting[0])){
            preg_match("/^\w+/", $input,$mx);
            showError("D??? li???u ?????u v??o sai", ['highlight'=>isset($mx[0]) ? $mx[0] : $input]);
            die;
        }
//        cammomdump($str_all_dai);
        $queryGetDai = "/((($str_all_dai) ?)+) ??(\d+)/";
//        if(isset($_GET['type']) && $_GET['type'] == 1){
//            //(((?<!\d)(kh) ?)+) ??(\d+)?
//            $queryGetDai = "/(((?<!\d)($str_all_dai) ?)+) ??(\d+)/"; //@todo: ????o nh??? ????i mi???n nam n??y l?? nh?? n??o
//        }
        $input = preg_replace_callback($queryGetDai, function($matches_callback){
            $dai = $matches_callback[0];
            $dai = preg_replace("/ ([234])(d) ?([\d]+)/"," $1dai $3", $dai);
            return $dai;

        }, $input);
//        cammomdump($input);

        preg_match_all($queryGetDai, $input, $matches_dai);

        // cammomdump($input);
//        cammomdump($all_dai);
//        cammomdump($matches_dai);
        if(!isset($matches_dai[1]) or (isset($matches_dai[1]) && empty($matches_dai[1]))){
            showError("kh??ng t??m th???y ????i n??o ph?? h???p", ['highlight'=>$input]);
            die;
        }
        $dai_da_tim_thay = $matches_dai[1];
//         cammomdump($matches_dai[1]);
//         cammomdump($queryGetDai);
        // chia c??c ????i ra c??c m???ng c??? ph??p.
        $this->kiemtraDauDuoi($input);
//        cammomdump($input);
        $cac_cu_phap = [];
        $this->ConvertOptinalArrayDai($dai_da_tim_thay, $input);
        foreach($dai_da_tim_thay as $indexDai => $dai){
            if(in_array(trim($dai),OPTIONAL_DAI)){
                // l???y ra N ????i ?????u ti??n.
                $this->converOptinalDai($dai, $input);
                $dai = trim($dai);
                $number = str_replace("d", "", $dai);
                $ndai = $this->getNDai();
                $_str_n_dai = [];
                for($i=0;$i<$number;$i++){
                    if(!isset($ndai[$i])){
                        showError("H??m nay ch??? c?? ". (count($this->getNDai())) ." ????i" , ['d'=> $this->getNDai()]);
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
        // step2: l???y ra c??c c??ch ch??i t??? c?? ph??p b??n tr??n.
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
        Check xem h??m nay c?? ????i n??y kh??ng
        return boolean.
    */
    private function checkDaiHomNay($ten_viet_tat){
        if($this->inputtype == BAC){
            return true;// b??? qua check ????i mi???n b???c
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
        Ki???m tra s??? ????nh c?? ph???i l?? s??? k??o kh??ng.
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
                    showError("S??? K??o $min -> $max kh??ng th??? ??t h??n 3 ch??? s???", ['highlight'=> $sodanh]);
                    die;
                }
            }
            if((int)$max <= (int)$min){
                showError("S??? K??o $max kh??ng th??? nh??? h??n ho???c b???ng s??? k??o $min", ['highlight'=> $sodanh]);
                die;
            }
            if($str_len_min != $str_len_max){
                showError("S??? k??o $min($str_len_min con) v?? $max($str_len_max con) kh??ng c??ng lo???i:", ['highlight'=> $sodanh]);
                die;
            }

            if($str_len_min >= 5){
                showError("T???i ??a ???????c ph??p k??o h??ng ngh??n", ['highlight'=> $sodanh]);
                die;
            }

            $sokeo_type = $str_len_max."con";
            if($loai_keo == 'khc'){
                $this->sosanhkeo($min, $max, 2, $sodanh);
                if($str_len_min < 2){
                    showError("S??? k??o h??ng ch???c ph???i t??? 2 s??? tr??? l??n", ['highlight'=> "{$min} v?? {$max}"]);
                    die;
                }
                if($min[strlen($min) - 1] != $max[strlen($max) - 1]){
                    showError("S??? k??o h??ng ch???c kh??ng gi???ng nhau {$min[strlen($min) - 1]} v?? {$max[strlen($max) - 1]}", ['highlight'=> $sodanh,'min'=>$min,'max'=>$max]);
                    die;
                }
            }

            if($loai_keo == 'kht'){
                $this->sosanhkeo($min, $max, 3, $sodanh);

                if($str_len_min < 3){
                    showError("S??? k??o h??ng ch???c ph???i t??? 3 s??? tr??? l??n", ['highlight'=> "{$min} v?? {$max}"]);
                    die;
                }
                if($min[strlen($min) - 1].$min[strlen($min) - 2] != $max[strlen($max) - 1].$max[strlen($max) - 2]){
                    showError("S??? k??o h??ng tr??m kh??ng gi???ng nhau {$min[strlen($min) - 1]}{$min[strlen($min) - 2]} v?? {$max[strlen($max) - 1]}{$max[strlen($max) - 2]}", ['highlight'=> $sodanh]);
                    die;
                }
            }

            if($loai_keo == 'khn'){
                $this->sosanhkeo($min, $max, 4, $sodanh);

                if($str_len_min < 4){
                    showError("S??? k??o h??ng ngh??n ph???i t??? 4 s??? tr??? l??n", ['highlight'=> "{$min} v?? {$max}"]);
                    die;
                }
                if($min[strlen($min) - 1].$min[strlen($min) - 2].$min[strlen($min) - 3] != $max[strlen($max) - 1].$max[strlen($max) - 2].$max[strlen($max) - 3]){
                    showError("S??? k??o h??ng ngh??n kh??ng gi???ng nhau", ['highlight'=> $sodanh]);
                    die;
                }
            }

            if($loai_keo == 'kl'){
                if($min % 2 == 0 || $max % 2 == 0){

                    // showError("S??? k??o L??? ph???i l?? s??? l???.", ['highlight'=> [$min, $max]]);
                    // die;

                }
            }

            if($loai_keo == 'kc'){
                if($min % 2 != 0 || $max % 2 != 0){

                    // showError("S??? k??o ch???n ph???i l?? s??? ch???n.", ['highlight'=> [$min, $max]]);
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
                    showError("S??? k??o sai c?? ph??p.", ['highlight'=> $sodanh]); die;
                }
            }
        }
    }


    /*
    T??ch s??? k??o t??? c?? ph??p ???? t??ch s?? b??? ??? @TachCuPhap()
    00k10 -> k??o t??? 00, 01 -> 10
    khc -> k??o h??ng ch???c: 11k31 -> 11 21 31
    */
    private function tachSoKeo(){

    }

    private function tachBinhThuong($data){
        // ki???m tra ????i.
        $result = [];
        foreach($data as $_normalItem){
            $dai = explode(" ", trim($_normalItem['dai']));
            foreach($dai as $_dai){
                $check_dai_hom_nay = $this->checkDaiHomNay($_dai);
                if($check_dai_hom_nay == false){
                    showError("-Ng??y h??m nay kh??ng c?? ????i [$_dai]", ['highlight'=> $_dai]);
                    die;
                }


                $data_sokeo = $this->phanTichSoKeo($_normalItem, $_dai);
                if($data_sokeo == false){
                    $strlen  = strlen($_normalItem['sodanh']);
                    if($strlen <= 1 || $strlen >= 5){
                        showError("S??? ????nh ph???i l?? 1 s??? t??? 2-4 ch??? s???" , ['highlight'=> $_normalItem['sodanh']]);
                        die;
                    }
                    $types = $this->getTypeBySoDanh($strlen."con");
                    if(in_array($_normalItem['cachdanh'], $types) == false){
                        showError("c??ch ????nh $strlen con kh??ng th??? ????nh {$_normalItem['cachdanh']}",['sodanh'=>$_normalItem['sodanh']]);
                        die;
                    }
                    // tr?????ng h???p s??? th?????ng, kh??ng ph???i s??? k??o.
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
                    // l?? type ?????o th?? ?????o.
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
        ????nh 2->4 ????i, ????nh t??? 2 s??? tr??? l??n : longan hcm  20 30 40 dax 30 -> longan 20 30 dax 30, longan 20 40 dax 30, long an 30 40 dax 30 ( hcm t????ng t??? )
    */
    private function tachDaXien($data){
        // ki???m tra xem s??? ????i.
        $first_data = $data[0];
        $dai  = explode(" ", trim($first_data['dai']));
        foreach($dai as $_dai){
            $_dai = trim($_dai);
            if(!empty($_dai)){
                $check_dai_hom_nay = $this->checkDaiHomNay($_dai);
                if($check_dai_hom_nay == false){
                    showError("--Ng??y h??m nay kh??ng c?? ????i [$_dai]", ['highlight'=> $_dai]);
                    die;
                }
            }

        }
        $so_dai = count($dai);
        if($so_dai < 2){
            if($this->inputtype != BAC){
                showError("S??? ????i trong ???? xi??n ph???i t??? 2 tr??? l??n",['highlight'=> is_array($dai)? implode(' ',$dai) : $dai]);
                die;
            }
        }
        $count_sodanh = count($data);
        $arr_sodanh = [];
        if($count_sodanh < 2){
            showError("c??ch ch??i ???? xi??n ph???i c?? 2 s??? tr??? l??n",['highlight'=> $data[0]['sodanh']]);
            die;
        }
        foreach($data as $daxien_item){
            $arr_sodanh[] = $daxien_item['sodanh'];
            if(strlen($daxien_item['sodanh']) != 2){
                showError("S??? trong c??ch ch??i ???? xi??n ph???i l?? s??? c?? 2 ch??? s???", ['highlight'=> $daxien_item['sodanh']]);
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
        ph???i ????nh 2 con s??? tr??? l??n, 2 con s??? t????ng ???ng v???i 1 l???nh, check l???i t??? h???p,ch??? dc ????nh s??? c?? 2 ch??? s???
    */
    private function tachDaThang($data){

        $count_sodanh = count($data);
        $arr_sodanh = [];
        if($count_sodanh < 2){
            showError("c??ch ch??i ???? th???ng ph???i c?? 2 s??? tr??? l??n", ['highlight'=>$data[0]['sodanh']]);
            die;
        }
        foreach($data as $dathang_item){
            $arr_sodanh[] = $dathang_item['sodanh'];
            if(strlen($dathang_item['sodanh']) != 2){
                showError("S??? trong c??ch ch??i ???? th???ng ph???i l?? s??? c?? 2 ch??? s???", ['highlight'=> $dathang_item['sodanh']]);
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
                showError("[3]Ng??y h??m nay kh??ng c?? ????i [{$__dai}]", ['highlight'=> $__dai]);
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
        $input = preg_replace('/\s\s+/', ' ', $input); // replace c??c kho???ng tr???ng li??n t???c v??? kho???ng tr???ng duy nh???t
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



