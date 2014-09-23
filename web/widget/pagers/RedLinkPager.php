<?php
/**
 * file:LinkPager.php
 * author:Toruneko@outlook.com
 * date:2014-7-6
 * desc: 分页
 */
class RedLinkPager extends CLinkPager{
	public $firstPageCssClass = '';
	public $lastPageCssClass = '';
	public $previousPageCssClass = '';
	public $nextPageCssClass = '';
	public $internalPageCssClass = '';
	public $hiddenPageCssClass = '';
	public $selectedPageCssClass = 'active';
	
	public function init(){
		if($this->nextPageLabel===null)
			$this->nextPageLabel=Yii::t('yii','&gt;');
		if($this->prevPageLabel===null)
			$this->prevPageLabel=Yii::t('yii','&lt;');
		if($this->firstPageLabel===null)
			$this->firstPageLabel=Yii::t('yii','&lt;&lt;');
		if($this->lastPageLabel===null)
			$this->lastPageLabel=Yii::t('yii','&gt;&gt;');
		if($this->header===null)
			$this->header=Yii::t('yii','');

		if(!isset($this->htmlOptions['id']))
			$this->htmlOptions['id']=$this->getId();
		if(!isset($this->htmlOptions['class']))
			$this->htmlOptions['class']='pagination';
		
		$this->cssFile = false; //禁用系统样式
	}
}