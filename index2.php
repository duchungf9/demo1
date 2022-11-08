<?php 

function cammomdump($data){
    highlight_string("<?php\n\$data =\n" . var_export($data, true) . ";\n?>");
}

function showError($mes, $opt = []){
    header('Content-Type: application/json; charset=utf-8');
    if(count($opt) == 0){
        echo json_encode(['error'=>1, 'data'=>$mes]); die;
    }else{
        echo json_encode(['error'=>1, 'data'=>$mes, 'opt'=>$opt]); die;
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

function phanTichCuPhap(string $dai, string $input, array $array_cacDai, int $indexDai): string
{
    
    $next = $array_cacDai[$indexDai + 1] ?? "";
    if (!empty($next)) {
        $query = "/$dai ?(.+) ($next)/";
    } else {
        $query = "/$dai ?(.+)/"; // đài cuối
    }
    preg_match_all($query, $input, $matches);
    return $matches[1][0] ?? "";
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
                'act'=>'apilottery',
                'type'=>0,
                'plus'=>'list_dai'
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

    private function cachChoiByDai($tenDai){

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

    // private function    phantichCachDanh($cuphap){
    //     $tail = $cuphap['body'];
    //     $dai = $cuphap['dai'];
    //     $full_cuphap = $dai  . " " . $tail;
    //     $str_type = implode("|", $this->danhSachAllCachChoi());
    //     // kiểm tra xem có phải là các cách đánh viết liền sau tên đài hay không?
    //     $query = '/((\d+ ?|\d{1,4}k\d{1,4}|\d{1,4}khc\d{1,4}|\d{1,4}khc\d{1,4})+ ?)+? ?('.$str_type.')+ ?(\d+)/';
    //     $query_full = '/'.$dai. ' (.*?) ?('.$str_type.')+ ?(\d+)/'; // query này sẽ lấy các số đánh tiengiang ?(.*?) ?(bay|lo|dat) \d+
    //     $query_2 = "/(($str_type) ?\d{1,3}[\s\S]?){2,}/";        // kiểm tra phần sau có phải là lặp cú pháp 3+4 (1, 2 giữ nguyên)
    //     $query_3 = "//"; // kiểm tra lặp 2 + 3 + 4 ()
    //     preg_match_all($query, $tail, $matches);
    //     preg_match_all($query_2, $tail, $matches2);
    //     preg_match_all($query_3, $tail, $matches3);
    //     preg_match_all($query_full, $full_cuphap, $matches_full);
    //     cammomdump($matches);
    //     // cammomdump($tail);
    //     // cammomdump($matches_full);
    //     $sodanh_string_full = trim($matches_full[1][0]);
    //     cammomdump($sodanh_string_full);
    //     $sodanh_array = explode(" ", $sodanh_string_full);
    //     $result = [];
    //     $cd_array = [];
    //     $data = [];
    //     if(empty($matches2[0])){
    //         // trường hợp chỉ có 1 cú pháp, không lặp cú pháp
    //         $not_matches = preg_split($query, $tail);
    //         if (!empty($not_matches[0])) {
    //             showError("Lỗi cú pháp ở đoạn ( không tồn tại cách đánh ){$not_matches[0]}",['hilight'=>$not_matches[0]]);
    //             die;
    //         }
    //         $array_cuphap_cachdanh = $matches[0];
    //         if (count($array_cuphap_cachdanh) == 0) {
    //             showError($this->getMessageWhenError($tail, $cuphap));
    //             die;

    //         }

    //         foreach ($array_cuphap_cachdanh as $index => $cuphap_cachdanh) {
    //             $result[] = $cuphap_cachdanh;
    //         }
            
    //         foreach($result as $key => $item){
    //             // $sodanh = trim($matches[1][$key]);
    //             foreach($sodanh_array as $_index => $_sodanh){
    //                 $sodanh = $sodanh_array[$_index];
    //                 $cachdanh = $matches[3][$key];
    //                 $tiendanh = $matches[4][$key];;
    //                 $data[] = [
    //                     'sodanh'=>$sodanh,
    //                     'cachdanh'=>$cachdanh,
    //                     'tiendanh'=>$tiendanh
    //                 ];
    //             }
                
    //         }
    //     }else{
    //         // tách cú pháp.
    //         $matched_string = $matches2[0][0];
    //         $not_matches = preg_split($query_2, $tail);
    //         if(!empty($not_matches[1])){
    //             showError("Lỗi cú pháp ở đoạn ( không tồn tại cách đánh ){$not_matches[1]}",['hilight'=>$not_matches[1]]);
    //         }
    //         // $query_get_sodanh = "/(\d{1,}|\d{2,4}k\d{2,4})+ ?$matched_string/";
    //         // preg_match_all($query_get_sodanh, $tail, $matches_sodanh);
    //         $sodanh = $sodanh_string_full;
    //         cammomdump($sodanh);
            
    //         $query_tach = "/($str_type) ?\d{1,}/";
    //         preg_match_all($query_tach, $matches2[0][0], $matches3);
    //         foreach($matches3[0] as $item){
    //             $result[] = $sodanh." ".$item;
    //         }
    //         if(!isset($sodanh)){
    //             showError("Không xác định được số đánh");
    //             die;
    //         }
    //         foreach($result as $item){
    //             $query2 = "/$sodanh_string_full ?([a-z]+) ?(\d+)/";
    //             preg_match_all($query2, $item, $__matches);
    //             $cachdanh = $__matches[3][0];
    //             $tiendanh = $__matches[4][0];
    //             $cd_array[] = $cachdanh;
    //             foreach($sodanh_array as $_index => $_sodanh){
    //                 $sodanh = $sodanh_array[$_index];
    //                 $data[] = [
    //                     'sodanh'=>$sodanh,
    //                     'cachdanh'=>$cachdanh,
    //                     'tiendanh'=>$tiendanh
    //                 ];
    //             }
    //             // $data[] = [
    //             //     'sodanh'=>$sodanh,
    //             //     'cachdanh'=>$cachdanh,
    //             //     'tiendanh'=>$tiendanh
    //             // ];
            
    //         }

    //         $unique_cachdanh = array_unique($cd_array);
        
    //         if(count($unique_cachdanh) != count($result)){
    //             showError("Cách đánh đang bị trùng");
    //             die;
    //         }
    //     }
        
        
        


    //     return $data;
    // }

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
        $query_ky_tu_non_digit = '/([^\d ]{1,}|\d{2,4}(k|khc|kht)\d{2,4})/'; // tìm các ký tự không phải là số trong chuỗi.
        preg_match_all($query_ky_tu_non_digit, $body, $ky_tu_non_digit);
        $this->kiemTraCachDanhHopLe($ky_tu_non_digit[0]); // bắt lỗi cách đánh không hợp lệ.
        $start_index_cach_danh = 0;
        $data = [];
        foreach($ky_tu_non_digit[0] as $_index => $_cach_danh){
            $_data_phan_tich_sodanh = $this->phanTichSoDanhDuaTrenCachDanh($_cach_danh, $body, $_index);
            foreach($_data_phan_tich_sodanh as $_item){
                // cammomdump($so_danh);
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
                    for($i = $max_index; $i >= 0; $i--){
                        foreach($data as $__item){
                            
                            if($__item['index'] == $i && $__item['sodanh'] != null){
                                    $so_danh_gan_nhat[] = $__item['sodanh'];
                            }
                        }
                    }

                    if(count($so_danh_gan_nhat) == 0){
                        showError("Không tìm được số đánh cho cách đánh [$cach_danh]",['highlight'=>$body]);
                        die;
                    }

                    foreach($so_danh_gan_nhat as $__so_danh_gan_nhat_single){
                        $__so_danh_gan_nhat_single_data = [
                            'dai'    => $dai,
                            'sodanh' => $__so_danh_gan_nhat_single,
                            'cachdanh'=>$_item['cachdanh'],
                            'tien'=> $_item['tien'],
                            'index' => $_item['index'],
                        ];

                        $data[] = $__so_danh_gan_nhat_single_data;
                    }


                }else{
                    $data[] = $_data_item;
                }

                
                
            }
           
        }

        // cammomdump($data);
        return $data;
        // $data = $this->phanTichSoDanhDuaTrenCachDanh($start_index_cach_danh,$ky_tu_non_digit[0], $body);
        

        
    }

    private function kiemTraCachDanhHopLe(&$cach_danh){
        $tat_ca_cachdanh = $this->danhSachAllCachChoi(); // lấy danh sách tất cả các cách đánh
        // kiểm tra xem có phải là số kéo không thì cũng bỏ qua.
        $query_so_keo = '/(\d{2,4}(k|khc|kht)\d{2,4})/';

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

        $query_so_danh = "/((.+) ?($cach_danh)) ?(\d+){1,1}/"; // lấy các số đứng trước $cach_danh
        preg_match_all($query_so_danh, $body_string, $matches_so_danh);
        $tiendanh = $matches_so_danh[4][0];
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

        
        
        die;
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
            showError("Không tìm thấy đài nào phù hợp trong văn bản");
            die;
        }
        $dai_da_tim_thay = $matches_dai[1];       
        // cammomdump($matches_dai[1]);
        // chia các đài ra các mảng củ pháp.
        $cac_cu_phap = [];
        foreach($dai_da_tim_thay as $indexDai => $dai){
            $dai = trim($dai);
            if(in_array($dai,['2d','3d','4d'])){
                // lấy ra N đài đầu tiên.
                $number = str_replace("d", "", $dai);
                $ndai = $this->getNDai();

                for($i=1;$i<=$number;$i++){
                    if(!isset($ndai[$i])){
                        showError("Hôm nay chỉ có ". ($i-1) ." đài"); 
                        die;
                    }
                    $cac_cu_phap[] = [
                        'dai'  => $ndai[$i],
                        'body' => trim(phanTichCuPhap($dai, $input, $dai_da_tim_thay, $indexDai))
                    ];
                }
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
    Tách số kéo từ cú pháp đã tách sơ bộ ở @TachCuPhap()
    00k10 -> kéo từ 00, 01 -> 10
    khc -> kéo hàng chục: 11k31 -> 11 21 31
    */
    private function tachSoKeo(){

    }

    /*

        đánh 2->4 đài, đánh từ 2 số trở lên : longan hcm  20 30 40 dax 30 -> longan 20 30 dax 30, longan 20 40 dax 30, long an 30 40 dax 30 ( hcm tương tự )
    */
    private function tachDaXien(){

    }

    /*
        phải đánh 2 con số trở lên, 2 con số tương ứng với 1 lệnh, check lại tổ hợp,chỉ dc đánh số có 2 chữ số
    */
    private function tachDaThang(){

    }

    public function verify(){
        $input = $_GET['s'];
        $cuphap_da_tach = $this->TachCuPhap($input);
        showSuccess($cuphap_da_tach);

    }
}


$app = new GrammarLesson();
$app->verify();



