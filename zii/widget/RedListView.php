<?php
/**
 * file:ListView.php
 * author:Toruneko@outlook.com
 * date:2014-7-6
 * desc: 列表
 */
Yii::import('zii.widgets.CBaseListView');
class RedListView extends CBaseListView{
	public $itemView;
	public $viewTag; // 传递额外的标记数据
	public $separator;
	public $viewData=array();

	public function init(){
		if($this->itemView===null)
			throw new CException(Yii::t('zii','The property "itemView" cannot be empty.'));
		parent::init();
	}
	
	public function run(){
		$this->registerClientScript();
		
		$this->renderContent();
	}

	/**
	 * Renders the data item list.
	 */
	public function renderItems()
	{
		$data=$this->dataProvider->getData();
		if(($n=count($data))>0)
		{
			$owner=$this->getOwner();
			$viewFile=$owner->getViewFile($this->itemView);
			$j=0;
			foreach($data as $i=>$item)
			{
				$data=$this->viewData;
				$data['index']=$i;
				$data['viewTag']=$this->viewTag;
				$data['data']=$item;
				$data['widget']=$this;
				$owner->renderFile($viewFile,$data);
				if($j++ < $n-1)
					echo $this->separator;
			}
		}
		else
			$this->renderEmptyText();
	}
}