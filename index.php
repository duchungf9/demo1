<?php
function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}


class N289
{
    public $input;
    public $dais, $cach_danh, $alldai;
    public $errors = null;

    public function __construct(string $input = null)
    {
        $this->input = $input;
        $this->getDaiApi();
    }

    private function getDaiApi(){
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

        $this->dais = $data->data->tendai;
        $this->cach_danh = $data->data->cachchoi;
        $this->alldai = $data->data->alldai;
    }
    private function getAllDai(): array
    {
        $data = $this->alldai;
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

    /*
     * get tên đài
     */
    private function getDai(): array
    {
        $data = $this->dais;
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

    private function getNDai(){
        $data = $this->dais;
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
        $data = $this->dais;
        $result = null;
        foreach($data as $item_array){
            foreach($item_array as $ten_dai => $items){
                foreach($items as $dai){
                    if($dai === $ten_viet_tat){
                        $result = $ten_dai;
                    }
                }
            }
        }

        return $result;
    }

    private function getCachChoi($ten_viet_tat){

        $data = $this->cach_danh;
        $result = null;
        foreach($data as $item_array){
            foreach($item_array as $ten_cach => $items){
                foreach($items as $item){
                    foreach($item as $it){
                        foreach($it as $i){
                            if($i === $ten_viet_tat){
                                $result = $ten_cach;
                            }
                        }

                    }

                }
            }
        }

        return $result;
    }

    private function getType(){
        $data = $this->cach_danh;
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
        $data = $this->cach_danh;
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

    /*
     * validate các đài
     */
    public function run(string $input)
    {

        $array_dai = $this->getAllDai();

        $str_dai = implode("|", $array_dai);
//        $this->varExpDie($str_dai);
        //((($str_dai) ?)+) ?\d+g
        $queryGetDai = "/((($str_dai) ?)+) ?\d+/";
        preg_match_all($queryGetDai, $input, $matches);
        $array_cacDai = $matches[1];


        if(count($array_cacDai) == 0){
            $this->errors = "dai khong ton tai: $str_dai" ;
        }

        $cuphap = [];
        foreach ($array_cacDai as $indexDai => $dai) {
            $dai = trim($dai);
            if(in_array($dai,['2d','3d','4d'])){
                // lấy ra N đài đầu tiên.
                $number = str_replace("d", "", $dai);
                $ndai = $this->getNDai();

                for($i=1;$i<=$number;$i++){
                    if(!isset($ndai[$i])){
                        echo json_encode(['m'=>'Hôm nay chỉ có ' . ($i-1) . " đài",'error'=>1]);die;
                    }
                    $cuphap[] = [
                        'dai'  => $ndai[$i],
                        'tail' => trim($this->getTheTailDai($dai, $input, $array_cacDai, $indexDai))
                    ];
                }
            }else{
                $cuphap[] = [
                    'dai'  => $dai,
                    'tail' => trim($this->getTheTailDai($dai, $input, $array_cacDai, $indexDai))
                ];
            }


        }
        foreach ($cuphap as &$_cp) {
            $_cp['cachdanh'] = $this->validateTail($_cp);
        }

        $this->getSoDanh($cuphap);
        $cuphap = array_sort($cuphap, 'dai', SORT_DESC);
        if(is_array($cuphap)){
            $_cloneCuphap = $cuphap;
            foreach($_cloneCuphap as &$_ccp){
                $_ccp['dai'] = ($this->getTenDai($_ccp['dai'])); // convert về chung 1 dai
            }
            $unique = array_unique($_cloneCuphap, SORT_REGULAR);
            if(count($_cloneCuphap) != count($unique)){
                $this->errors = 'Đài bị trùng';
            }
        }
        $result = $cuphap;
        if($this->errors != null){
            $result[] = ['error'=> 1, 'mes' => $this->errors];
        }


        echo json_encode($result);

    }


    /*
     * Tách ra nhiều số dựa vào cú pháp.
     */
    private function getSoDanh(array &$cuphap){
        $result = [];
        $type_str = "(".implode("|", $this->getType()).")";
        foreach($cuphap as $cp){
            $cachdanh = $cp['cachdanh'];
            if(is_array($cachdanh)){
                //(\d+|\d\d\d\dk\d\d\d\d) ?+(bay|bao|dd) ?(\d{1,})
                $query = '/(.+) ?+'. $type_str .' ?(\d{1,})/';
                $typeDanh_array = [];
                foreach($cachdanh as $item){
                    preg_match_all($query, $item, $matches);
                    $not_matches = preg_split($query, $item);
                    if(isset($not_matches[0]) && $not_matches[0] != ''){
                        echo json_encode(['mes'=>'khong ton tai cach danh ' . $not_matches[0] . "('cac cach danh hop le: $type_str')",'error'=>1]); die;
                    }

                    $array_sodanh = explode(" ",$matches[1][0]);

                    $array_dai = explode(" ", $cp['dai']);
                    $tiendanh = $matches[3][0];
                    $typedanh = $matches[2][0];

                    if($typedanh == null || empty($typedanh)){
                        echo json_encode(['mes'=>"Không tồn tại cách đánh: " . $cp['cachdanh']]);
                    }else{
                    }
                    $typeDanh_array[] = $typedanh;
                    foreach($array_sodanh as $sodanh){
                        if(!empty($sodanh)){
                            foreach($array_dai as $dai){
                                $shortcut_name = $dai;
                                $dai = $this->getTenDai($dai);
                                if(!in_array($shortcut_name, $this->getDai())){
                                    echo json_encode(['mes'=> "Hôm nay không có đài: " . $shortcut_name,'error'=>1]); die;
                                }
                                //kiểm tra có phải số kéo không?
                                $get_so_keo = $this->getSoKeo($sodanh);
                                if(count($get_so_keo) > 0){
                                    // trường hợp số kéo.
                                    foreach($get_so_keo as $sokeo){
                                        $result[] = [
                                            'tendai' => $dai,
                                            'dai' => $shortcut_name,
                                            'sodanh' => $sokeo,
                                            'cachdanh'=> $cp['cachdanh'],
                                            'tiendanh'=>$tiendanh,
                                            'type'=>$typedanh,
                                            '_' => 'sokeo'
                                        ];
                                    }

                                }else{
                                    // kiểm tra xem là 2 con hay 3, 4 con
                                    $strlen  = strlen($sodanh);
                                    $types = $this->getTypeBySoDanh($strlen."con");
                                    if(in_array($typedanh, $types) == false){

                                        $cp['cachdanh'] = "cách đánh " . $strlen."con" . " không thể đánh " . $typedanh . "(".implode("|", $types).")";
                                    }
                                    if(!is_int((int)$sodanh)){
                                        echo json_encode(['mes'=>$sodanh . " Khong phai la so danh",'error'=>1]); die;
                                    }

                                    $result[] = [
                                        'tendai' => $dai,
                                        'dai' => $shortcut_name,
                                        'sodanh' => $sodanh,
                                        'cachdanh'=> $cp['cachdanh'],
                                        'tiendanh'=>$tiendanh,
                                        'type'=>$typedanh,
                                        '_'=>'normal'
                                    ];
                                }
                            }


                        }

                    }
                    foreach($typeDanh_array as &$_type){
                        $_type = $this->getCachChoi($_type);
                    }
                    $unique_typedanh_array = array_unique($typeDanh_array);
                    if(count($unique_typedanh_array) != count($typeDanh_array)){
                        echo json_encode(['mes'=>'Có cách đánh bị trùng','error'=>1]);
                    }
                }


            }else{
                $result[] = [
                    'dai' => $cp['dai'],
                    'sodanh' => "Sai cú pháp",
                    'cachdanh'=> $cp['cachdanh'],
                    'tiendanh'=>"",
                    'type'=>"",
                ];
            }

            $cuphap = $result;
        }
    }

    private function getMessageWhenError(string $tail, array $cuphap)
    {
        $messages = [];
//      '/((\d+)+ ?)+? ([a-z]+) (\d+)/';
        $query_thieu_sodanh = '/[a-z]+ \d+/'; // cú pháp thiếu phần 1
        preg_match_all($query_thieu_sodanh, $tail, $matches_sodanh);
        if (!empty($matches_sodanh[0])) {
            $messages[] = "Thiếu số đánh";
        }

        $query_thieu_cachdanh = '/\d+? \d+/'; // cú pháp thiếu phần 1
        preg_match_all($query_thieu_cachdanh, $tail, $matches_cachdanh);
        if (!empty($matches_cachdanh[0])) {
            $messages[] = "Thiếu cách đánh";
        }

        $query_thieu_tiendanh = '/\d+? [a-z]+/'; // cú pháp thiếu phần 1
        preg_match_all($query_thieu_tiendanh, $tail, $matches_tiendanh);
        if (!empty($matches_tiendanh[0])) {
            $messages[] = "Thiếu tiền đánh";
        }

        if (count($messages) == 0) {
            return "Cú pháp phần đánh của đài {$cuphap['dai']} bị sai";
        }
        return implode(", ", $messages);
    }


    private function validateTail(array $cuphap)
    {
        $tail = $cuphap['tail'];

        $str_type = implode("|", $this->getType());
        // kiểm tra xem có phải là các cách đánh viết liền sau tên đài hay không?
        $query = '/((\d+|\d{1,4}k\d{1,4})+ ?)+? ?[a-z]+ ?\d+/';
        $query_2 = "/(($str_type) ?\d{1,3}[\s\S]?){2,}/";        // kiểm tra phần sau có phải là lặp cú pháp 3+4 (1, 2 giữ nguyên)
        preg_match_all($query, $tail, $matches);
        preg_match_all($query_2, $tail, $matches2);
        $result = [];
        if(empty($matches2[0])){
            $not_matches = preg_split($query, $tail);
            if (!empty($not_matches[0])) {
                echo json_encode(['mes'=>"Lỗi cú pháp ở đoạn {$not_matches[0]}: {$query}",'error'=>1]);
            }
            $array_cuphap_cachdanh = $matches[0];

            if (count($array_cuphap_cachdanh) == 0) {
                echo json_encode(['mes'=>$this->getMessageWhenError($tail, $cuphap),'error'=>1]);


            }

            foreach ($array_cuphap_cachdanh as $cuphap_cachdanh) {
                $result[] = $cuphap_cachdanh;
            }
        }else{
            // tách cú pháp.
            $matched_string = $matches2[0][0];
            $not_matches = preg_split($query_2, $tail);
            if(!empty($not_matches[1])){
                echo json_encode(['mes'=>"[2]Sai cú pháp ở: {$not_matches[1]}",'error'=>1]);
            }
            $query_get_sodanh = "/(\d{1,})+ $matched_string/";
            preg_match_all($query_get_sodanh, $tail, $matches_sodanh);
            $sodanh = $matches_sodanh[1][0];

            $query_tach = "/($str_type) ?\d{1,}/";
            preg_match_all($query_tach, $matches2[0][0], $matches3);
            foreach($matches3[0] as $item){
                $result[] = $sodanh." ".$item;
            }
        }

        $cd_array = [];
        foreach($result as $item){
            $query2 = '/((\d+|\d{1,4}k\d{1,4})+ ?)+? ?([a-z]+) ?\d+/';
            preg_match_all($query2, $item, $__matches);
            $cachdanh = $__matches[3][0];
            $cd_array[] = $cachdanh;
        }

        $unique_cachdanh = array_unique($cd_array);
        if(count($unique_cachdanh) != count($result)){
            echo json_encode(['m'=> 'Cách đánh bị trùng','error'=>1]); die;
        }



        return $result;
    }


    private function getSoKeo(string $string_soKeo): array
    {
        $result = [];
        $query = '/([0-9]{2,4})k([0-9]{2,4})/';
        $regex = preg_match_all($query, trim($string_soKeo), $matches);

        if (!empty($matches[1][0]) && !empty($matches[2][0])) {
            $from = (int)$matches[1][0];
            $to = (int)$matches[2][0];
            if((int)$to > (int)$from){
                $this->errors = "Ngay keo khong dung";
            }
            for ($i = $from; $i <= $to; $i++) {
                $result[] = $this->padNum($i);
            }
        }

        return $result;
    }

    private function getTheTailDai(string $dai, string $input, array $array_cacDai, int $indexDai): string
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

    /*
     * chỉ lấy ra các cú pháp chứa các đài đã config.
     */
    private function validateDai(array &$array_cacDai, array $array_dai)
    {
        foreach ($array_cacDai as $key => $item) {
            $string_dais = explode(" ", trim($item));
            foreach ($string_dais as $dai) {
                if (!in_array($dai, $array_dai)) {
                    echo "Đài " . $dai . " Không tồn tại <br/>";
                    unset($array_cacDai[$key]);
                }
            }
        }

        $array_cacDai = array_values($array_cacDai);
    }

    private function padNum(int $number): string
    {
        return str_pad($number, 4, 0, STR_PAD_LEFT);
    }


    private function varExp($vars)
    {
        echo '<pre>' . var_export($vars, true) . '</pre>';

    }

    private function varExpDie($vars)
    {
        return $this->varExp($vars);
        die();
    }


}


$_old = isset($_GET['s']) ? $_GET['s'] : "";
$loadModel = new N289();
//$loadModel->getSoKeo("0000k9999");
//echo "<style>table, th, td {
//  border: 1px solid black;
//}</style>";
//echo "<form method='GET' action='/'>
//            <textarea name='s' cols='100' rows='20'>{$_old}</textarea>
//            <button type='submit'>Test</button>
//    </form>";
header('Content-Type: application/json; charset=utf-8');

echo $loadModel->run($_old);

//function checkFnc($input_lines)
//{
//    if (isset($_GET['s'])) {
//        $input_lines = $_GET['s'];
//    }
//    $input_lines = str_replace("  ", " ", trim($input_lines));
//    $regex = preg_match_all('/(([a-zA-Z]+ ?)+|(\d{2}[d]) |(\d{1}[d])) ?+((\d+ ?)+) ?([a-zA-Z]+) ?(\d+)/', trim($input_lines), $output_array);
//    $result = [];
//    foreach ($output_array as $group_key => $group_matches) {
//        foreach ($group_matches as $key => $item) {
//            $result[$key][] = $item;
//        }
//    }
//
//    $matches = [];
//    foreach ($result as $match) {
//        $matches[] = getCorrect($match, $input_lines);
//    }
//    $output_string = $input_lines;
//    $message = "";
//    $correct_items = [];
//    $wrong_items = [];
//    foreach ($matches as $single_match) {
//        $correct_items[] = $single_match[0];
//        $output_string = str_replace($single_match[0], "__CORRECT_ITEM__", $output_string);
//
//        $text_map_array = [1 => "phần 1", 2 => "phần 2", 3 => "phần 3", 4 => "phần 4"];
//        foreach ($text_map_array as $index => $value) {
//            if (empty($single_match[$index])) {
//                $message .= $value . ", ";
//            }
//        }
//    }
////    echo "<span style='color: red'>" . $output_string . "</span>" . (($message != "") ? "Không xác định được ". $message : "");
////    echo "<br/>";
//    $wrong_items = explode("__CORRECT_ITEM__", $output_string);
//    echo "<br/>";
//    echo "Những phần đúng <br/>";
//    foreach ($correct_items as $item) {
//        echo $item . "<br/>";
//    }
//    echo "<br/>";
////
////    echo "Những phần sai";
////    foreach($wrong_items as $item){
////        echo $item . "<br/>";
////    }
//    foreach ($wrong_items as $item) {
//        secondCheck($item);
//    }
//    $_old = isset($_GET['s']) ? $_GET['s'] : "";
//    echo "<form method='GET' action='/'>
//            <textarea name='s' cols='100' rows='20'>{$_old}</textarea>
//            <button type='submit'>Test</button>
//    </form>";
//
//}
//
//function secondCheck($input_lines)
//{
//    $input_lines = str_replace("  ", " ", trim($input_lines));
////    $regex = preg_match_all('/(([a-zA-Z]+ ?)+|(\d{2}[d]) |(\d{1}[d])) ?+((\d+ ?)+) ?([a-zA-Z]+) ?(\d+)/', trim($input_lines), $output_array);
//    $regex_w_1 = preg_match_all('/((\d+ ?)+) ?([a-zA-Z]+) ?(\d+)/', trim($input_lines), $output_array1);
//    $regex_w_2 = preg_match_all('/(([a-zA-Z]+ ?)+|(\d{2}[d]) |(\d{1}[d])) ?+([a-zA-Z]+) ?(\d+)/', trim($input_lines), $output_array2);
//    $regex_w_3 = preg_match_all('/(([a-zA-Z]+ ?)+|(\d{2}[d]) |(\d{1}[d])) ?+((\d+ ?)+) ?(\d+)/', trim($input_lines), $output_array3);
//    $regex_w_4 = preg_match_all('/(([a-zA-Z]+ ?)+|(\d{2}[d]) |(\d{1}[d])) ?+((\d+ ?)+) ?([a-zA-Z]+) ?/', trim($input_lines), $output_array4);
//    foreach ($output_array1[0] as $_item_1) {
//        if ($_item_1 != "") {
//            echo $_item_1 . "( thiếu phần 1)<br/>";
//        }
//    }
//    foreach ($output_array2[0] as $_item_1) {
//        if ($_item_1 != "") {
//            echo $_item_1 . "( thiếu phần 2)<br/>";
//        }
//    }
//    foreach ($output_array3[0] as $_item_1) {
//        if ($_item_1 != "") {
//            echo $_item_1 . "( thiếu phần 3)<br/>";
//        }
//    }
//    foreach ($output_array4[0] as $_item_1) {
//        if ($_item_1 != "") {
//            echo $_item_1 . "( thiếu phần 4)<br/>";
//        }
//    }
//
//
//}
//
//
//function getCorrect($match, $input_lines)
//{
//    return ([$match[0], $match[1], $match[5], $match[7], $match[8]]);
//}


//checkFnc("dc ct 44 99 da30 dd100 b30 99 dd500 9944 b10 baodao5 ok
//91 81 13 b10 da15 391 381 b10 xc30 313 b10 xc20 9391 8381 b5 ok
//9900 6969 b1 ok
//16 61 da15 16 b50 32 duoi 300 ok
//49 67 27 65 da2 dd30 49 27 b25 765 b5 xc30 583 b3 16 61 b20 da10 ok");



