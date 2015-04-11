<?php
/**
 * File: SaeSearch.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 14/10/31 09:33
 * Description: 
 */
class SaeSearchEngine extends RedSearchEngine{
    public $disabled_tags = array(
        SaeSegment::POSTAG_ID_UNKNOW,   //未知词
        //连词 30-32
        SaeSegment::POSTAG_ID_C,        //连词
        SaeSegment::POSTAG_ID_C_N,      //体词连接词
        SaeSegment::POSTAG_ID_C_Z,      //分句连接词
        //感叹词 50
        SaeSegment::POSTAG_ID_E,        //感叹词
        //数词 90
        SaeSegment::POSTAG_ID_M,        //数词
        //拟声词 107
        SaeSegment::POSTAG_ID_O,        //拟声词
        //介词 108
        SaeSegment::POSTAG_ID_P,        //介词
        //代词 120-127
        SaeSegment::POSTAG_ID_R,        //代词
        SaeSegment::POSTAG_ID_R_D,      //副词性代词
        SaeSegment::POSTAG_ID_R_M,      //数词性代词
        SaeSegment::POSTAG_ID_R_N,      //名词性代词
        SaeSegment::POSTAG_ID_R_S,      //处所词性代词
        SaeSegment::POSTAG_ID_R_T,      //时间词性代词
        SaeSegment::POSTAG_ID_R_Z,      //谓词性代词
        SaeSegment::POSTAG_ID_R_B,      //区别词性代词
        //助词 140-146
        SaeSegment::POSTAG_ID_U,        //助词
        SaeSegment::POSTAG_ID_U_N,      //定语助词
        SaeSegment::POSTAG_ID_U_D,      //状语助词
        SaeSegment::POSTAG_ID_U_C,      //补语助词
        SaeSegment::POSTAG_ID_U_Z,      //谓词后助词
        SaeSegment::POSTAG_ID_U_S,      //体词后助词
        SaeSegment::POSTAG_ID_U_SO,     //助词("所")
        //标点符号 150-156
        SaeSegment::POSTAG_ID_W,        //标点符号
        SaeSegment::POSTAG_ID_W_D,      //顿号
        SaeSegment::POSTAG_ID_W_H,      //中缀型符号
        SaeSegment::POSTAG_ID_W_L,      //搭配型标点左部
        SaeSegment::POSTAG_ID_W_R,	    //搭配型标点右部
        SaeSegment::POSTAG_ID_W_S,	    //分句尾标点
        SaeSegment::POSTAG_ID_W_SP,	    //句号
        //语气词 160
        SaeSegment::POSTAG_ID_Y,        //语气词
        //动词 173-176
        SaeSegment::POSTAG_ID_V_SH,     //动词“是”
        SaeSegment::POSTAG_ID_V_YO,     //动词“有”
        SaeSegment::POSTAG_ID_V_Q,      //趋向动词
        SaeSegment::POSTAG_ID_V_A,      //助动词
        //语素词 190-196
    	SaeSegment::POSTAG_ID_X,        //语素词
        SaeSegment::POSTAG_ID_X_N,      //名词语素
        SaeSegment::POSTAG_ID_X_V,      //动词语素
        SaeSegment::POSTAG_ID_X_S,      //处所词语素
        SaeSegment::POSTAG_ID_X_T,      //时间词语素
        SaeSegment::POSTAG_ID_X_Z,      //状态词语素
        SaeSegment::POSTAG_ID_X_B,      //状态词语素
        //代量短语 202
        SaeSegment::POSTAG_ID_RQ,       //代量短语
        //空格 230
        SaeSegment::POSTAG_ID_SPACE,    //空格
    );

    public function init(){
        parent::init();

        $this->segment = new SaeSegment();
    }
}
