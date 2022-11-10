<?php 

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
    public function __construct()
    {
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
        $result[] = '2d';
        $result[] = '3d';
        $result[] = '4d';
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
        $result[] = '2d';
        $result[] = '3d';
        $result[] = '4d';
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
        $els = ( array_unique( array_diff_assoc( $array, array_unique($array))));
        if(count($els) > 0){
            showError("Có cách đánh bị trùng", ['hightlight'=> $cuphap['body']]);
            die;
        }
    }


    private function phantichCachDanh2($cuphap){
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
        $query_ky_tu_non_digit = '/([^\d ]{1,}|\d{1,}(k|khc|kht)\d{1,})/'; // tìm các ký tự không phải là số trong chuỗi.
        preg_match_all($query_ky_tu_non_digit, $body, $ky_tu_non_digit);
        $this->kiemTraCachDanhHopLe($ky_tu_non_digit[0]); // bắt lỗi cách đánh không hợp lệ.
        $start_index_cach_danh = 0;
        $data = [];

        $this->timCachDanhBiTrung($ky_tu_non_digit[0], $cuphap);

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


        return $data;
        // $data = $this->phanTichSoDanhDuaTrenCachDanh($start_index_cach_danh,$ky_tu_non_digit[0], $body);
        

        
    }

    private function kiemTraCachDanhHopLe(&$cach_danh){
        $tat_ca_cachdanh = $this->danhSachAllCachChoi(); // lấy danh sách tất cả các cách đánh
        // kiểm tra xem có phải là số kéo không thì cũng bỏ qua.
        $query_so_keo = '/(\d{1,}(k|khc|kht)\d{1,})/';
        foreach($cach_danh as $index=>$word){
            preg_match_all($query_so_keo, $word, $matches_sokeo);
            if(!in_array($word, $tat_ca_cachdanh) && empty($matches_sokeo[0][0])){
                showError("Cách đánh [$word] không tồn tại",['highlight'=>$word, 'avaiable'=> $tat_ca_cachdanh]);
                die;
            }

            if(!empty($matches_sokeo[0][0])){
                // bỏ số kéo ra khỏi cách đánh
                unset($cach_danh[$index]);
            }
        }

    }

    private function phanTichSoDanhDuaTrenCachDanh($cach_danh, &$body_string, $index){

        $query_so_danh = "/((.+)? ?($cach_danh)) ?(\d+){1,1}/"; // lấy các số đứng trước $cach_danh
        preg_match_all($query_so_danh, $body_string, $matches_so_danh);
        $tiendanh = $matches_so_danh[4][0] ?? "";
        if(empty($tiendanh)){
            showError("Không xác định được tiền đánh trong cách đánh [$cach_danh]", ['highlight'=>$cach_danh]);
            die;
        }
        $so_danh = $matches_so_danh[2][0];
        $body_string = str_replace($matches_so_danh[0][0],"", $body_string); // bỏ phần đã tìm được ra khỏi body
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

    /*
    // phần này sẽ tách cả chuỗi input ra thành từng bộ phận sau đó mới tới các step sau.
    */
    private function TachCuPhap($input){
        // step1: (đài{1,})(số-đánh{1,}|số-kéo)(cách-đánh)(tiền-đánh{1})
        $all_dai = $this->danhSachAllDai();
        $str_all_dai = implode("|", $all_dai);
        $queryGetDai = "/((($str_all_dai) ?)+) ?\d+/";
        preg_match_all($queryGetDai, $input, $matches_dai);
        if(!isset($matches_dai[1]) or (isset($matches_dai[1]) && empty($matches_dai[1]))){
            showError("Không tìm thấy đài nào phù hợp trong văn bản", ['q'=>$queryGetDai]);
            die;
        }
        $dai_da_tim_thay = $matches_dai[1];       
        // cammomdump($matches_dai[1]);
        // chia các đài ra các mảng củ pháp.
        $cac_cu_phap = [];
        foreach($dai_da_tim_thay as $indexDai => $dai){
            if(in_array(trim($dai),['2d','3d','4d'])){
                // lấy ra N đài đầu tiên.
                $dai = trim($dai);
                $number = str_replace("d", "", $dai);
                $ndai = $this->getNDai();
                $_str_n_dai = [];
                for($i=1;$i<=$number;$i++){
                    if(!isset($ndai[$i])){
                        showError("Hôm nay chỉ có ". ($i-1) ." đài"); 
                        die;
                    }
                    $_str_n_dai[] = $ndai[$i];
                
                }
                
                $cac_cu_phap[] = [
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

    /*
        Check xem hôm nay có đài này không
        return boolean.
    */
    private function checkDaiHomNay($ten_viet_tat){
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
        $query = "/(((\d{1,})(k|khc|kht)(\d{1,})))/";
        preg_match_all($query, $sodanh, $matches);
        if(!empty($matches[1][0])){
            $min = trim($matches[3][0]);
            $max = trim($matches[5][0]);
            $str_len_min  = strlen($min);
            $str_len_max = strlen($max);
            if((int)$max <= (int)$min){
                showError("Số Kéo $max không thể nhỏ hơn hoặc bằng số kéo $min", ['highlight'=> $sodanh]);
                die;
            }
            if($str_len_min != $str_len_max){
                showError("Số kéo $min($str_len_min con) và $max($str_len_max con) không cùng loại:", ['highlight'=> $sodanh]);
                die;
            }

            if($str_len_min >= 4){
                showError("Tối đa được phép kéo hàng trăm", ['highlight'=> $sodanh]);
                die;
            }

            $sokeo_type = $str_len_max."con";
            if($sokeo_type == "2con"){
               if($min[1] != $max[1]){
                showError("Số kéo hàng chục không giống nhau {$min[1]} và {$max[1]}", ['highlight'=> $sodanh]);
                die;
               }
            }

            if($sokeo_type == "3con"){
                if($min[1].$min[2] != $max[1].$max[2]){
                 showError("Số kéo hàng trăm không giống nhau {$min[1]}{$min[2]} và {$min[1]}{$max[2]}", ['highlight'=> $sodanh]);
                 die;
                }
            }
            $result = [];
            $increment_num = 0;
            switch($sokeo_type){
                case "1con":
                    $increment_num = 1;
                    break;
                case "2con":
                    $increment_num = 10;
                    break;
                case "3con":
                    $increment_num = 100;
                    break;        
            }

            for($i=$min; $i<=$max;$i+=$increment_num){
                $result[] = [
                    'dai'     => $_dai,
                    'cachdanh'=> $_normalItem['cachdanh'],
                    'sodanh'  => (int)$i,
                    'tien'    => $_normalItem['tien'],
                    'index'   => $_normalItem['index'],
                    'keydai'=> $this->getTenDai($_dai),

                    'keydanh'=>$this->layCachChoi($_normalItem['cachdanh'])

                ];
            }

            
            return $result;
        }
        return false;
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
            // cammomdump($_normalItem);
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
            showError("Số đài trong đá xiên phải từ 2 trở lên",['highlight'=> $dai]);
            die;
        }
        $count_sodanh = count($data);
        $arr_sodanh = [];
        if($count_sodanh < 2){
            showError("cách chơi đá xiên phải có 2 số trở lên");
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
                showError("cách chơi đá thẳng phải có 2 số trở lên");
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

    public function verify(){
        $input = $_GET['s'];
        $input = preg_replace('/\s\s+/', ' ', $input); // replace các khoảng trắng liên tục về khoảng trắng duy nhất
        $cuphap_da_tach = $this->TachCuPhap($input);
        showSuccess($cuphap_da_tach);
        die;

    }
}


$app = new GrammarLesson();
$app->verify();



