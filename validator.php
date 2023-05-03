<?php
/**
 * Created by PhpStorm.
 * User: LEGION
 * Date: 2/26/2023
 * Time: 12:29 AM
 */

class Validator
{

    public $input = null;
    public $knowed = [];
    CONST ALL_DAI = "tp|tpho|tph|hochiminh|dt|dth|dthap|dongthap|cm|cmau|camau|bt|btre|btr|bentre|vt|vtau|vungtau|bli|blieu|baclieu|dn|dnai|dongnai|ct|ctho|cth|cantho|st|strang|str|soctrang|tn|tninh|tayninh|ag|agiang|angiang|bt|bth|bthuan|binhthuan|vl|vlong|vlog|vinhlong|bd|bduong|sb|sbe|songbe|binhduong|tv|tving|trv|travinh|la|lan|longan|bp|bphuoc|bph|binhphuoc|hg|hgiang|haugiang|tg|tgiang|tiengiang|kg|kgiang|kiengiang|dl|dlat|dalat|haidai|badai|bondai|(^|\s)2d|(^|\s)3d|(^|\s)4d|(^|\s)2dai|(^|\s)3dai|(^|\s)4dai";
    CONST ALL_CACHDANH = "dau|dui|duoi|de|dauduoi|dd|daudui|bao|baolo|lo|dat|dathang|dav|daxien|dax|xien|xi|da|dx|dxv|davong|dxvong|dv|baylo|bay|baobaylo|bbaylo|xcdau|xdau|xchudau|xiuchudau|tldau|dauxc|daux|dauxiu|dauxiuchu|dautl|xiudau|xcdaudao|xdaudao|xchudaudao|xiuchudaudao|tldaudao|dauxcd|dauxcdao|dauxd|dauxdao|dauxiud|dauxiudao|dauxiuchud|dauxiuchudao|dautld|dautldao|xiudaudao|daoxiudau|xcdui|xdui|xchudui|xiuchudui|xcduoi|xchuduoi|xduoi|xiuchuduoi|tldui|tlduoi|bacang|cang|duixc|duix|duixiu|duixiuchu|duitl|duoixc|duoix|duoixiu|duoixiuchu|duoitl|xiudui|xiuduoi|xcduidao|xduidao|xchuduidao|xiuchuduidao|tlduidao|tlduoidao|xcduoidao|xduoidao|xchuduoidao|xiuchuduoidao|duixcd|duixcdao|duixd|duixdao|duixiud|duixiudao|duixiuchud|duixiuchudao|duitld|duitldao|duoixcd|duoixcdao|duoixd|duoixdao|duoixiud|duoixiudao|duoixiuchud|duoixiuchudao|duoitld|duoitldao|xiuduidao|xiuduoidao|daoxiuduoi|daoxiuduoi|xc|xchu|xiuchu|x|tl|xieu|xiu|baodao|baolodao|bldao|bdao|lodao|bld|daob|db";
    CONST CHECK_ONE = "/(" . self::ALL_DAI . "|" . self::ALL_CACHDANH . ")\s?(\d+)n?/";
    CONST CHECK_DAI_SODANH = "/(".self::ALL_DAI.")\s?(\d+)/";
    CONST CHECK_DAI_SODANH_CACHDANH_DAI_FAIL_1 = "/(".self::ALL_DAI.")(".self::NEGATIVE_LOOKING_BACK_FOR_DAI.")\s?(\d+\s?)+\s(".self::ALL_CACHDANH.")".self::NEGATIVE_LOOKING_BACK_FOR_CACHDANH."\s(".self::ALL_DAI.")(".self::NEGATIVE_LOOKING_BACK_FOR_DAI.")/"; // thiếu tiền đánh
    CONST NEGATIVE_LOOKING_BACK_FOR_DAI = "(?!a\s?\d+|d|sau|ui|uoi|e|auduoi|dui|audui|ao|aolo|lo|at|athang|av|axien|ax|xien|xi|a|x|dx|xv|avong|xvong|v|baylo|bay|baobaylo|bbaylo|xcdau|xdau|xchudau|xiuchudau|tldau|auxc|aux|auxiu|auxiuchu|auxiuchudau|autl|xiudau|cdaudao|daudao|chudaudao|iuchudaudao|ldaudao|uxcd|uxcdao|uxd|uxdao|uxiud|uxiudao|uxiuchud|uxiuchudao|autld|iudaudao|aoxiudau)";
    CONST NEGATIVE_LOOKING_BACK_FOR_CACHDANH = "(?!uong|ao)";
    CONST CHECK_DAI_SODANH_FAIL_1 = "/(".self::ALL_DAI.")(".self::NEGATIVE_LOOKING_BACK_FOR_DAI.")\s?(\d+ ?)+?\s?(\d+)n/"; // trường hợp đài + số + tiền có N ( báo thiếu cách đánh )
    CONST CHECK_DAI_SODANH_FAIL_2 = "/(".self::ALL_DAI.")(".self::NEGATIVE_LOOKING_BACK_FOR_DAI.")\s?(\d+ ?)+?\s?(\d+n|".self::ALL_DAI.")".self::NEGATIVE_LOOKING_BACK_FOR_DAI."/"; // đài + số đánh + đài ( thiếu cách đánh, tiền đánh )
    CONST CHECK_DAI_SODANH_FAIL_3 = "/(".self::ALL_DAI.")(".self::NEGATIVE_LOOKING_BACK_FOR_DAI.")\s?(\d{1,}(k|khc|kht|kc|kl|khn)\d{1,} ?){1,}?\s?(\d+n|".self::ALL_DAI.")/"; // đài + số kéo + đài ( thiếu cách đánh, tiền đánh )
    CONST CHECK_SO_N_TIEN_FAIL = "/(\d+ ){2,}(\d+n)/"; // trường hợp các số liền nhau, kết thúc bằng tiền + N
    CONST CHECK_SODANH_DOUBLE_FAIL = "/(\d+n) ?(\d+)? (\d+n)/";
    CONST CHECK_D_N_BLANK_OR_DAI_FAIL = "/(".self::ALL_CACHDANH.")".self::NEGATIVE_LOOKING_BACK_FOR_CACHDANH."\s?(\d+)n?\s(\d+) ?(".self::ALL_DAI."|$)".self::NEGATIVE_LOOKING_BACK_FOR_DAI."/";
    CONST CHECK_D_N_BLANK_OR_DAI_FAIL_2 = "/(".self::ALL_CACHDANH.")".self::NEGATIVE_LOOKING_BACK_FOR_CACHDANH."\s?(\d+)n?\s(\d+) ?(".self::ALL_CACHDANH.")".self::NEGATIVE_LOOKING_BACK_FOR_CACHDANH."$/";
    CONST CHECK_D_N_C_D_DN = "/(".self::ALL_DAI.")(".self::NEGATIVE_LOOKING_BACK_FOR_DAI.")\s?(\d{1,}(k|khc|kht|kc|kl|khn)\d{1,}\s?|\d+\s?)+?\s?(".self::ALL_CACHDANH.")".self::NEGATIVE_LOOKING_BACK_FOR_CACHDANH."\s(\d+n|\d+)\s(\d+n|\d+)\s?$/";
    CONST CHECK_DAI_FAIL_CACHDANH = "/(".self::ALL_DAI.")(".self::NEGATIVE_LOOKING_BACK_FOR_DAI.")\s?(".self::ALL_CACHDANH.")".self::NEGATIVE_LOOKING_BACK_FOR_CACHDANH."/";
    CONST CHECK_SO_SO_THAPPHAN = "/((\d+ ?)+ ?){3,} \d+\.\d+/"; //10 20 0.5 hoặc 10 20 30 0.5
    // trường hợp đài + số + số thì phải check xem kế tiếp nếu lại là đài hoặc tiền thì cũng sai
    CONST ANGIANG_FAIL = "/[^\d|^\s]ag/";
    CONST CHECK_CACHDANH_TIEN = "/(".self::ALL_CACHDANH.")\s?(\d+)/";

    public function __construct($input)
    {
        $this->input = $input;
    }

    public function validate()
    {
        return $this->stepOne();
    }

    /*
     * Lấy các số có khả năng là tiền đánh ( chữ đứng trước số có 2 trường hợp là : cách đánh + tiền | đài + số đánh
     */
    private function stepOne()
    {
        $this->CHECK_DAI_FAIL_CACHDANH();
        $this->CHECK_SO_SO_THAPPHAN();
        $this->daiSo();
        $this->daiSoSo();
        $this->doubleSoDanh();
        $this->checkDaiSoCachDai();
        $this->cachTien();
        $this->sodanh_n_tien();
        $this->CHECK_D_N_BLANK_OR_DAI_FAIL();
        $this->CHECK_D_N_C_D_DN();
        $this->ANGIANG_FAIL();

//        echo self::ALL_CACHDANH;
//        print_r($this->knowed);die;
        $output = "";
        foreach($this->knowed as $knowed_item){
            $output .= $knowed_item['msg'] . " position: {$knowed_item['start']} tới {$knowed_item['end']} ({$knowed_item['text']})<br/>";
        }

        //echo $output; die;
        return $this->knowed;
    }

    /*
     *  check xem cú pháp đài + số
     *  sau số thì có thể là : số, số kéo, cách đánh
     *  nếu là cách đánh hoặc 1 ký tự chưa biết thì báo sai nhé.
     */
    private function daiSo()
    {
        preg_match_all(self::CHECK_DAI_SODANH_FAIL_1, $this->input, $matches2, PREG_OFFSET_CAPTURE);
        if (isset($matches2[0]) && count($matches2[0]) > 0) {
            foreach($matches2[0] as $theMatch){
                $theMatches = $theMatch;
                $this->knowed[] = ['text'=>$theMatches[0], 'start'=> $theMatches[1] , 'end' => (int)$theMatches[1] + strlen($theMatches[0]), 'type'=>'daiso', 'msg'=>'Lỗi thiếu cách đánh!'];
            }
        }
    }

    private function daiSoSo(){
        preg_match_all(self::CHECK_DAI_SODANH_FAIL_2, $this->input, $matches2, PREG_OFFSET_CAPTURE);
        preg_match_all(self::CHECK_DAI_SODANH_FAIL_3, $this->input, $matches3, PREG_OFFSET_CAPTURE);
        if (isset($matches2[0]) && count($matches2[0]) > 0) {
            foreach($matches2[0] as $theMatch){
                $theMatches = $theMatch;
                $this->knowed[] = ['text'=>$theMatches[0], 'start'=> $theMatches[1] , 'end' => (int)$theMatches[1] + strlen($theMatches[0]), 'type'=>'daiso', 'msg'=>'Lỗi thiếu cách đánh.'];
            }
        }
        if (isset($matches3[0]) && count($matches3[0]) > 0) {
            foreach($matches3[0] as $theMatch3){
                $theMatches = $theMatch3;
                $this->knowed[] = ['text'=>$theMatches[0], 'start'=> $theMatches[1] , 'end' => (int)$theMatches[1] + strlen($theMatches[0]), 'type'=>'daiso', 'msg'=>'Lỗi thiếu Cách đánh.'];
            }
        }
    }



    private function CHECK_D_N_C_D_DN(){
        preg_match_all(self::CHECK_D_N_C_D_DN, $this->input, $matches2, PREG_OFFSET_CAPTURE);
        if (isset($matches2[0]) && count($matches2[0]) > 0) {
            foreach($matches2[0] as $theMatch){
                $theMatches = $theMatch;
                $this->knowed[] = ['text'=>$theMatches[0], 'start'=> $theMatches[1] , 'end' => (int)$theMatches[1] + strlen($theMatches[0]), 'type'=>'daiso', 'msg'=>'Số đánh không đúng..'];
            }
        }
    }

    private function CHECK_DAI_FAIL_CACHDANH(){
        preg_match_all(self::CHECK_DAI_FAIL_CACHDANH, $this->input, $matches2, PREG_OFFSET_CAPTURE);
        if (isset($matches2[0]) && count($matches2[0]) > 0) {
            foreach($matches2[0] as $theMatch){
                $theMatches = $theMatch;
                $this->knowed[] = ['text'=>$theMatches[0], 'start'=> $theMatches[1] , 'end' => (int)$theMatches[1] + strlen($theMatches[0]), 'type'=>'daiso', 'msg'=>'Thiếu số đánh..'];
            }
        }
    }
    private function CHECK_SO_SO_THAPPHAN(){
        preg_match_all(self::CHECK_SO_SO_THAPPHAN, $this->input, $matches2, PREG_OFFSET_CAPTURE);
        if (isset($matches2[0]) && count($matches2[0]) > 0) {
            foreach($matches2[0] as $theMatch){
                $theMatches = $theMatch;
                $this->knowed[] = ['text'=>$theMatches[0], 'start'=> $theMatches[1] , 'end' => (int)$theMatches[1] + strlen($theMatches[0]), 'type'=>'daiso', 'msg'=>'Lỗi thiếu cách đánh..'];
            }
        }
    }

    private function CHECK_D_N_BLANK_OR_DAI_FAIL(){
        preg_match_all(self::CHECK_D_N_BLANK_OR_DAI_FAIL, $this->input, $matches2, PREG_OFFSET_CAPTURE);
        preg_match_all(self::CHECK_D_N_BLANK_OR_DAI_FAIL_2, $this->input, $matches3, PREG_OFFSET_CAPTURE);
        if (isset($matches2[0]) && count($matches2[0]) > 0) {
            foreach($matches2[0] as $theMatch){
                $theMatches = $theMatch;
                $this->knowed[] = ['text'=>$theMatches[0], 'start'=> $theMatches[1] , 'end' => (int)$theMatches[1] + strlen($theMatches[0]), 'type'=>'daiso', 'msg'=>'thiếu cách chơi hoặc số ko đúng!'];
            }
        }
        if (isset($matches3[0]) && count($matches3[0]) > 0) {
            foreach($matches3[0] as $theMatch){
                $theMatches = $theMatch;
                $this->knowed[] = ['text'=>$theMatches[0], 'start'=> $theMatches[1] , 'end' => (int)$theMatches[1] + strlen($theMatches[0]), 'type'=>'daiso', 'msg'=>'thiếu cách chơi hoặc số ko đúng'];
            }
        }
    }

    private function sodanh_n_tien(){
        preg_match_all(self::CHECK_SO_N_TIEN_FAIL, $this->input, $matches2, PREG_OFFSET_CAPTURE);
        if (isset($matches2[0]) && count($matches2[0]) > 0) {
            foreach($matches2[0] as $theMatch){
                $theMatches = $theMatch;
                $this->knowed[] = ['text'=>$theMatches[0], 'start'=> $theMatches[1] , 'end' => (int)$theMatches[1] + strlen($theMatches[0]), 'type'=>'daiso', 'msg'=>'Không thấy cách đánh'];
            }
        }
    }
    private function ANGIANG_FAIL(){
        preg_match_all(self::ANGIANG_FAIL, $this->input, $matches2, PREG_OFFSET_CAPTURE);
        if (isset($matches2[0]) && count($matches2[0]) > 0) {
            foreach($matches2[0] as $theMatch){
                $theMatches = $theMatch;
                $this->knowed[] = ['text'=>$theMatches[0], 'start'=> $theMatches[1] , 'end' => (int)$theMatches[1] + strlen($theMatches[0]), 'type'=>'daiso', 'msg'=>'Đài hoặc số đánh không đúng..'];
            }
        }
    }

    private function doubleSoDanh(){
        preg_match_all(self::CHECK_SODANH_DOUBLE_FAIL, $this->input, $matches2, PREG_OFFSET_CAPTURE);
        if (isset($matches2[0]) && count($matches2[0]) > 0) {
            foreach($matches2[0] as $theMatch){
                $theMatches = $theMatch;
                $this->knowed[] = ['text'=>$theMatches[0], 'start'=> $theMatches[1] , 'end' => (int)$theMatches[1] + strlen($theMatches[0]), 'type'=>'daiso', 'msg'=>'Số đánh không đúng.'];
            }
        }
    }
    private function checkDaiSoCachDai(){ // đài + số + cách đánh + đài
        preg_match_all(self::CHECK_DAI_SODANH_CACHDANH_DAI_FAIL_1, $this->input, $matches2, PREG_OFFSET_CAPTURE);
        if (isset($matches2[0]) && count($matches2[0]) > 0) {
            foreach($matches2[0] as $theMatch){
                $theMatches = $theMatch;
                $this->knowed[] = ['text'=>$theMatches[0], 'start'=> $theMatches[1] , 'end' => (int)$theMatches[1] + strlen($theMatches[0]), 'type'=>'daiso', 'msg'=>'Thiếu tiền đánh.'];
            }
        }
    }



    private function cachTien()
    {
//        preg_match_all(self::CHECK_CACHDANH_TIEN, $match[0], $matches2);
//        if (count($matches2[0]) > 0) {
//            $this->knowed[] = ['text'=>$match[0], 'start'=> $match[1] , 'end' => (int)$match[1] + strlen($match[0]), 'type'=>'cachtien'];
//        }
    }

    private function hilight($type){
        $map = ['daiso'=>'red', 'cachtien'=>'blue'];
        return $map[$type];
    }
}