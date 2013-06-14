<?php
abstract class CrudController extends FwController
{
	protected function getViewRight()
	{
		return '';
	}
	protected function getEditRight()
	{
		return '';
	}
	
	protected abstract function getObjClass();
	
	protected function getObjName()
	{
		$classname = $this->getObjClass();
		return $classname::ObjName;
	}
	
	protected function getObjPk()
	{
		$classname = $this->getObjClass();
		return $classname::getPrimaryKey();
	}
	
	protected function getObjTable()
	{
		$classname = $this->getObjClass();
		return $classname::GetTable();
	}
	
	public function Init(IModule $module)
	{
		parent::Init($module);
	
		$this->checkPrivilege($this->getViewRight());
	}
	
	// Need override
	protected function getListData(&$totalCount)
	{
		$query = new QueryBuilder();
		return $query->from($this->getObjTable())
			->orderBy($this->getObjPk(), 'desc')
			->fetchWithCount($totalCount, $this->_offset, $this->_perpage);
	}

	// Need override
	protected function getTableFields()
	{
		return array(
				//field, fieldname, type, sorttype,align,properties
				//array('name','名字','date','asc','left',$properties),
// 				array('entranceid','ID','','asc','', null),
// 				array('entrancename','入口名字','','asc','', null),
// 				array('alianame','内部代号','','asc','', null),
// 				array('entrancehost','入口地址','','asc','', null),
// 				array('openvpnport','OpenVPN端口','','asc','', null),
// 				array('entranceisactive','启用','bool','asc','', null),
// 				array('cp','操作','cp','','', array(
// 						'设置'=>array('','edit','entranceid','', 'btn btn-mini'),
// 						'删除'=>array('','delete','entranceid','','btn btn-mini'),
// 						)),
		);
	}
	
	protected function bindTableButtons(UIHtmlTable $table)
	{
		$table->BindHeadbar(Fw::Html()->Button('add', '新建', 'link', $this->GetUrl('','add')));
	}
	
	/**
	 * 
	 * @param UIForm $form
	 * @param DataObject $object
	 */
	protected function bindFormButtons( $form,  $object)
	{
	}
	
	protected function getFormGroups($object=null)
	{
		return array(
// 				'DEFAULT'=>array('基本设置',true),
// 				'RADIUS'=>array('Radius设置',false),
// 				'PPTP'=>array('PPTP设置',false),
// 				'OPEN'=>array('OpenVPN设置',false),
				);
	}

	protected function getFormFields($object=null)
	{
		return array(
			//field, fieldname, edittype, rules, properties, default
			//array('entrancename','名字','text','require|string',null, ),
// 			array('entrancename','入口名字','text','require|string',null, ),
// 			//array('alianame','内部代号','text','string',null, ),
// 			array('entrancehost','入口地址','text','require',null, ),
// 			array('entranceisactive','是否启用','checkbool','int',null, ),
			
// 			array('RADIUS/mysqlhost','数据库地址|Mysql数据库地址','text','require',null, ),
// 			array('RADIUS/mysqldbname','数据库名','text','require',null, ),
// 			array('RADIUS/mysqluser','数据库用户名','text','require',null, ),
// 			array('RADIUS/mysqlpass','数据库密码','text','require',null, ),
				
// 			//array('PPTP/pptpport','PPTP端口','text','require|int',null, ),
// 			array('PPTP/pptpnet','PPTP网段|留空则不预先分配IP','ip_input','ip',null,),
// 			array('PPTP/pptpmask','PPTP掩码|留空则不预先分配IP','ip_input','ip',null, ),
				
// 			array('OPEN/openvpnport','OpenVPN拨号端口','text','require|int|positive',null,),
// 			array('OPEN/openvpnmanport','OpenVPN管理端口','text','require|int|positive',null,),
// 			array('OPEN/openvpnnet','OpenVPN网段|留空则不预先分配IP','ip_input','ip',null,),
// 			array('OPEN/openvpnmask','OpenVPN掩码|留空则不预先分配IP','ip_input','ip',null,),
		);
	}
	
	public function indexAction()
	{
		return $this->listAction();
	}
	
	protected function getTableTitle()
	{
		return $this->getObjName()."列表";
	}
	
	public function listAction()
	{
		$table = new UIHtmlTable($this->getTableTitle(),'list');
		
		$totalcount = 0;
		$list = $this->getListData($totalcount);
		
		$fields = $this->getTableFields();
		foreach($fields as $field)
		{
			//array('name','名字','date','asc','left',$properties),
			$table -> BindField($field[0], $field[1], $field[2], $field[3], $field[4], $field[5]);
		}
	
		$table -> BindData($list);
		if($this->_perpage>0)
		{
			$table -> BindPager($totalcount, $this->_perpage, $this->_page);
		}
		
		$this->bindTableButtons($table);
				
		$this->GetView()->Assign('content', $table->GetBody());
		$this->GetView()->Display($this->getTemplate());
	}

	public function deleteAction()
	{
		$this->checkPrivilege($this->getEditRight());
		$ids = $this->getInput($this->getObjPk(), DT_ARRAY);
		if (!$ids)
		{
			$ids[] = $this->getInput($this->getObjPk(),'int|require','没有指定要删除的'.$this->getObjName());
		}
	
		foreach ($ids as $id)
		{
			$objclass = $this->getObjClass();
			$obj = new $objclass($id);
			if (!$obj->getData())
			{
				$this->Message("没有找到要删除的".$this->getObjName()." ".$id, LOCATION_REFERER);
			}
			$rs = $obj->getData();
			if(!$obj -> delete())
			{
				$this->beforeDelete($obj);
				$this->Message("删除 ".$this->getObjName()."失败", LOCATION_REFERER);
			}
		}
		$this->Message("删除".$this->getObjName()."成功", LOCATION_REFERER);
	}
	
	public function addAction()
	{
		$this->checkPrivilege($this->getEditRight());

		//$this->pageTitle = "新建".$this->getObjName();
		$this->returnUrl = $this->getUrl('', 'list');
		
		$form = new UIForm('add', $this->getObjName()." [新建]", array('class'=>'uiform'), $this);
		$classname = $this->getObjClass();
		
		//@var $object DataObject
		$object = new $classname;

		$this->beforeRenderForm($form, $object);
		
		$this->bindFormButtons($form, $object);
		
		$form -> BindModel($object);

		$groups = $this->getFormGroups($object);
		foreach($groups as $groupId=>$group)
		{
			$form->BindGroup($groupId, $group[0],$group[1]);
		}
		
		$fields = $this->getFormFields($object);
		foreach($fields as $field)
		{
			//array('entrancename','名字','text','require|string',null, default),
			if($field[5]!==null)
			{
				$object->$field[0] = $field[5];
			}
			$form -> BindField($field[0],$field[1],$field[2],$field[3],$field[4]);
		}
		$form->BindFieldEx('referer', '', $this->GetReferer(),'hidden','skip');
		
		if($form->IsPostback()
				&& $form->Validate($this->GetRequest()))
		{
			$this->beforeSave($object);
			if($object->SaveData())
			{
				$this->afterSave($object);
				$this->Message("添加{$this->getObjName()}成功", $this->GetReferer());
			}
			else
			{
				$form->BindError('', '保存数据失败');
			}
		}
		$content = $form->GetBody();
		$this->GetView()->Assign('content', $form->GetBody());
		$this->GetView()->Display($this->getTemplate());
	}
	
	public function editAction()
	{
		//@var $object DataObject
		$this->checkPrivilege($this->getEditRight());
		$objId = $this->GetInput($this->getObjPk(),'require|int');
		$classname = $this->getObjClass();
		$object = new $classname($objId);
		if(!$object->GetData())
		{
			$this->Message("要修改的{$this->getObjName()}无效");
		}
		//$this->pageTitle = "修改".$this->getObjName();
		$this->returnUrl = $this->GetUrl('', 'list');
		$form = new UIForm('edit', $this->getObjName()." [Id:{$object->GetId()}]", 
			array('class'=>'uiform','subTitle'=>"ID:{$object->GetId()}"), $this);
		
		$this->beforeRenderForm($form, $object);
		
		$form -> BindModel($object);
		//$form -> BindField($this->getObjPk(),'ID','');
		$groups = $this->getFormGroups($object);
		foreach($groups as $groupId=>$group)
		{
			$form->BindGroup($groupId, $group[0],$group[1]);
		}
		$fields = $this->getFormFields($object);
		foreach($fields as $field)
		{
			//array('entrancename','名字','text','require|string',null, ),
			$form -> BindField($field[0],$field[1],$field[2],$field[3],$field[4]);
		}
		$form->BindFieldEx('referer', '', $this->GetReferer(),'hidden','skip');
		
		$this->bindFormButtons($form, $object);
		
		if($form->IsPostback() && $form->Validate($this->GetRequest()))
		{
			$this->beforeSave($object);
			if($object->SaveData())
			{
				$this->afterSave($object);
				//$this->Message("修改{$this->getObjName()}成功", $this->GetReferer());
				$form->BindSuccess("修改{$this->getObjName()}成功");
			}
			else
			{
				$form->BindError('', '保存数据失败');
			}
		}
		
		$this->GetView()->Assign('content', $form);
		$this->GetView()->Display($this->getTemplate());
	}
	
	protected function beforeRenderForm($form, $object)
	{
		//Nothing to do
	}
	
	protected function beforeSave($object)
	{
		//Nothing to do
	}
	
	protected function afterSave($object)
	{
		//Nothing to do
	}
	
	protected function beforeDelete($object)
	{
		//Nothing to do
	}
}