<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\cms\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\cms\model\Document;
use think\Db;

/**
 * 内容控制器
 * @package app\cms\admin
 */
class Content extends Admin
{
    /**
     * 空操作，用于显示各个模型的文档列表
     * @author 蔡伟明 <314013107@qq.com>
     */
    public function _empty()
    {
        cookie('__forward__', $_SERVER['REQUEST_URI']);
        $model_name = $this->request->action();
        $model      = Db::name('cms_model')->where('name', $model_name)->find();
        if (!$model) $this->error('找不到该内容');

        // 独立模型
        if ($model['type'] == 2) {
            $table_name = substr($model['table'], strlen(config('database.prefix')));

            // 查询
            $map = $this->getMap();
            $map['trash'] = 0;

            // 排序
            $order = $this->getOrder('id desc,update_time desc');
            // 数据列表
            $data_list = Db::view($table_name, true)
                ->view("cms_column", ['name' => 'column_name'], 'cms_column.id='.$table_name.'.cid', 'left')
                ->view("admin_user", 'username', 'admin_user.id='.$table_name.'.uid', 'left')
                ->where($map)
                ->order($order)
                ->paginate();

            $trash_count = Db::table($model['table'])->where('trash', 1)->count();

            // 自定义按钮
            $btnRecycle = [
                'title' => '回收站('.$trash_count.')',
                'icon'  => 'fa fa-trash',
                'class' => 'btn btn-info',
                'href'  => url('recycle/index', ['model' => $model['id']])
            ];
            $columns = Db::name('cms_column')->where(['model' => $model['id']])->column('id,name');

			switch ($model['name']) {

                        
                        case "town":
                        
                            // 使用ZBuilder快速创建数据表格
                            return ZBuilder::make('table')
                            ->setSearch(['title' => '乡镇名称']) // 设置搜索框
                            ->addColumns([ // 批量添加数据列
                            ['id', 'ID'],                            
                            ['title', '乡镇名称', 'link', url('cms/document/detail', ['id'=>'__id__','model' => $model['id']])],                                                      
                            ['create_time', '创建时间','datetime'],
                            ['sort', '顺序','text.edit'],  
                            ['right_button', '操作', 'btn']
                            ])
                            ->setTableName($table_name)
                            ->addTopButton('add', ['href' => url('document/add', ['model' => $model['id']])]) // 添加顶部按钮
                            //->addTopButton('enable', ['href' => url('document/enable', ['table' => $table_name])]) // 添加顶部按钮
                            //->addTopButton('disable', ['href' => url('document/disable', ['table' => $table_name])]) // 添加顶部按钮
                            ->addTopButton('delete', ['href' => url('document/delete', ['table' => $table_name])]) // 添加顶部按钮
                            ->addTopButton('custom', $btnRecycle) // 添加顶部按钮
                            
                            ->addRightButton('edit', ['href' => url('document/edit', ['model' => $model['id'], 'id' => '__id__'])]) // 添加右侧按钮
//                             ->addRightButton('delete', ['href' => url('document/delete', ['ids' => '__id__', 'table' => $table_name])]) // 添加右侧按钮
                           ->addRightButtons('delete')
                            ->addOrder('id,title,cid,view,username,update_time')
                            ->addFilter('cid', $columns)
                            ->addFilter(['username' => 'admin_user'])
                            ->addFilterMap(['cid' => ['model' => $model['id']]])
                            ->setRowList($data_list) // 设置表格数据
                            ->fetch(); // 渲染模板
                            break;
                                                        
                            case "student":
                        
                            // 使用ZBuilder快速创建数据表格
                            return ZBuilder::make('table')
                            ->setSearch(['title' => '学生姓名']) // 设置搜索框
                            
                        	->setPageTips('提示：点击家庭联系人查看该学生的全部家庭成员')
                            ->addColumns([ // 批量添加数据列
                            ['id', 'ID'],                            
                            //	['id', 'ID', 'link', url('cms/document/detail', ['id'=>'__id__','model' => $model['id']])],
                            ['title', '姓名', 'link', url('cms/document/detail', ['id'=>'__id__','model' => $model['id']])],
                            ['tbxx', '填报学校'],                          
                            ['tbsj', '填报时间'],
                            ['zp', '照片','picture'],
                            ['sex', '性别','status', '', ['女', '男']], 
                            ['jtlxr', '家庭联系人', 'link', url('cms/document/family', ['id'=>'__id__','model' => $model['id']])],
                            ['lxdh', '联系电话'],                        
                            ['right_button', '操作', 'btn']
                            ])
                            ->setTableName($table_name)
                            ->addTopButton('add', ['href' => url('document/add', ['model' => $model['id']])]) // 添加顶部按钮
                            //->addTopButton('enable', ['href' => url('document/enable', ['table' => $table_name])]) // 添加顶部按钮
                            //->addTopButton('disable', ['href' => url('document/disable', ['table' => $table_name])]) // 添加顶部按钮
                            ->addTopButton('delete', ['href' => url('document/delete', ['table' => $table_name])]) // 添加顶部按钮
                            ->addTopButton('custom', $btnRecycle) // 添加顶部按钮
                            
                            //->addRightButton('custom', ['href' => url('edit', ['id' => '__id__', 'group' => 'index'])])
                            
                            ->addRightButton('edit', ['href' => url('document/edit', ['model' => $model['id'], 'id' => '__id__'])]) // 添加右侧按钮
//                             ->addRightButton('delete', ['href' => url('document/delete', ['ids' => '__id__', 'table' => $table_name])]) // 添加右侧按钮
                            ->addRightButtons('delete')
                            ->addOrder('id,title,cid,view,username,update_time')
                            ->addFilter('cid', $columns)
                            ->addFilter(['username' => 'admin_user'])
                            ->addFilterMap(['cid' => ['model' => $model['id']]])
                            ->setRowList($data_list) // 设置表格数据
                            ->fetch(); // 渲染模板
                            break;
                            
                            
                            case "education":
                        
                            // 使用ZBuilder快速创建数据表格
                            return ZBuilder::make('table')
                            ->setSearch(['title' => '学历名称']) // 设置搜索框
                            ->addColumns([ // 批量添加数据列
                            ['id', 'ID'],
                            //['title', '项目名称','detail',['model' => $model['id']],'_blank','','id'],
                            ['title', '学历名称', 'link', url('cms/document/detail', ['id'=>'__id__','model' => $model['id']])],
                            ['create_time', '创建时间','datetime'],
                            //['update_time', '更新时间','datetime'],                            
                            ['sort', '顺序','text.edit'],
                            ['right_button', '操作', 'btn']
                            ])
                            ->setTableName($table_name)
                            ->addTopButton('add', ['href' => url('document/add', ['model' => $model['id']])]) // 添加顶部按钮
                            //->addTopButton('enable', ['href' => url('document/enable', ['table' => $table_name])]) // 添加顶部按钮
                            //->addTopButton('disable', ['href' => url('document/disable', ['table' => $table_name])]) // 添加顶部按钮
                            ->addTopButton('delete', ['href' => url('document/delete', ['table' => $table_name])]) // 添加顶部按钮
                            ->addTopButton('custom', $btnRecycle) // 添加顶部按钮
                            
                            ->addRightButton('edit', ['href' => url('document/edit', ['model' => $model['id'], 'id' => '__id__'])]) // 添加右侧按钮
//                             ->addRightButton('delete', ['href' => url('document/delete', ['ids' => '__id__', 'table' => $table_name])]) // 添加右侧按钮
                            ->addRightButtons('delete')
                            ->addOrder('id,title,cid,view,username,update_time')
                            ->addFilter('cid', $columns)
                            ->addFilter(['username' => 'admin_user'])
                            ->addFilterMap(['cid' => ['model' => $model['id']]])
                            ->setRowList($data_list) // 设置表格数据
                            ->fetch(); // 渲染模板
                            break;
                            
                            case "studentfamily":
                        
                            // 使用ZBuilder快速创建数据表格
                            return ZBuilder::make('table')
                            ->setSearch(['title' => '家长名字']) // 设置搜索框
                            ->addColumns([ // 批量添加数据列
                            ['id', 'ID'],
                            //['title', '动态名称','detail',['model' => $model['id']],'_blank','','id'],
                            ['title', '家属姓名', 'link', url('cms/document/detail', ['id'=>'__id__','model' => $model['id']])],
                            ['create_time', '创建时间','datetime'],
                            //['update_time', '更新时间','datetime'],
                            ['relation', '关系'],
                            ['zhiye', '职业'],
                            ['heath', '身体状况'],
                            ['other', '其他情况'], 
                            ['right_button', '操作', 'btn']
                            ])
                            ->setTableName($table_name)
                            ->addTopButton('add', ['href' => url('document/add', ['model' => $model['id']])]) // 添加顶部按钮
                            //->addTopButton('enable', ['href' => url('document/enable', ['table' => $table_name])]) // 添加顶部按钮
                            //->addTopButton('disable', ['href' => url('document/disable', ['table' => $table_name])]) // 添加顶部按钮
                            ->addTopButton('delete', ['href' => url('document/delete', ['table' => $table_name])]) // 添加顶部按钮
                            ->addTopButton('custom', $btnRecycle) // 添加顶部按钮
                            ->addRightButton('edit', ['href' => url('document/edit', ['model' => $model['id'], 'id' => '__id__'])]) // 添加右侧按钮
//                             ->addRightButton('delete', ['href' => url('document/delete', ['ids' => '__id__', 'table' => $table_name])]) // 添加右侧按钮
                            ->addRightButtons('delete')
                            ->addOrder('id,title,cid,view,username,update_time')
                            ->addFilter('cid', $columns)
                            ->addFilter(['username' => 'admin_user'])
                            ->addFilterMap(['cid' => ['model' => $model['id']]])
                            ->setRowList($data_list) // 设置表格数据
                            ->fetch(); // 渲染模板
                            break;
                            
                            case "yl_zhaopinqishi":
                        
                            // 使用ZBuilder快速创建数据表格
                            return ZBuilder::make('table')
                            ->setSearch(['title' => '职位']) // 设置搜索框
                            ->addColumns([ // 批量添加数据列
                            //['id', 'ID'],
                            //['title', '职位名称','detail',['model' => $model['id']],'_blank','','id'],
                            ['title', '职位名称', 'link', url('cms/document/detail', ['id'=>'__id__','model' => $model['id']])],
                            ['create_time', '创建时间','datetime'],
                            //['update_time', '更新时间','datetime'],
                            ['area', '招聘地区'],
                            ['number', '招聘人数'],
                            ['salary', '薪资待遇'],
                            ['duty', '工作职责'],
                            ['requirement', '工作要求'],
                            ['status', '是否发布','switch'], 
                            ['right_button', '操作', 'btn']
                            ])
                            ->setTableName($table_name)
                            ->addTopButton('add', ['href' => url('document/add', ['model' => $model['id']])]) // 添加顶部按钮
                            //->addTopButton('enable', ['href' => url('document/enable', ['table' => $table_name])]) // 添加顶部按钮
                            //->addTopButton('disable', ['href' => url('document/disable', ['table' => $table_name])]) // 添加顶部按钮
                            ->addTopButton('delete', ['href' => url('document/delete', ['table' => $table_name])]) // 添加顶部按钮
                            ->addTopButton('custom', $btnRecycle) // 添加顶部按钮
                            ->addRightButton('edit', ['href' => url('document/edit', ['model' => $model['id'], 'id' => '__id__'])]) // 添加右侧按钮
//                             ->addRightButton('delete', ['href' => url('document/delete', ['ids' => '__id__', 'table' => $table_name])]) // 添加右侧按钮
                            ->addRightButtons('delete')
                            ->addOrder('id,title,create_time')
                            ->addFilter('cid', $columns)
                            ->addFilter(['username' => 'admin_user'])
                            ->addFilterMap(['cid' => ['model' => $model['id']]])
                            ->setRowList($data_list) // 设置表格数据
                            ->fetch(); // 渲染模板
                            break;
                            
                                default:
            // 使用ZBuilder快速创建数据表格
            return ZBuilder::make('table')
                ->setSearch(['title' => '标题', 'cms_column.name' => '栏目名称']) // 设置搜索框
                ->addColumns([ // 批量添加数据列
                    ['id', 'id'],
                    //['title', '标题'],
                    ['title', '标题', 'link', url('cms/document/detail', ['id'=>'__id__','model' => $model['id']])],
                    ['create_time', '创建时间','datetime'],
                    ['update_time', '创建时间','datetime'],
                    ['right_button', '操作', 'btn']
                ])
                ->setTableName($table_name)
                ->addTopButton('add', ['href' => url('document/add', ['model' => $model['id']])]) // 添加顶部按钮
                ->addTopButton('enable', ['href' => url('document/enable', ['table' => $table_name])]) // 添加顶部按钮
                ->addTopButton('disable', ['href' => url('document/disable', ['table' => $table_name])]) // 添加顶部按钮
                ->addTopButton('delete', ['href' => url('document/delete', ['table' => $table_name])]) // 添加顶部按钮
                ->addTopButton('custom', $btnRecycle) // 添加顶部按钮
                ->addRightButton('edit', ['href' => url('document/edit', ['model' => $model['id'], 'id' => '__id__'])]) // 添加右侧按钮
                ->addRightButton('delete', ['href' => url('document/delete', ['ids' => '__id__', 'table' => $table_name])]) // 添加右侧按钮
                ->addOrder('id,title,cid,view,username,update_time')
                ->addFilter('cid', $columns)
                ->addFilter(['username' => 'admin_user'])
                ->addFilterMap(['cid' => ['model' => $model['id']]])
                ->setRowList($data_list) // 设置表格数据
                ->fetch(); // 渲染模板
                                break;

            
            }


        } else {
            // 查询
            $map = $this->getMap();
            $map['cms_document.trash'] = 0;
            $map['cms_document.model'] = $model['id'];
            // 排序
            $order = $this->getOrder('update_time desc');
            // 数据列表
            $data_list = Document::getList($map, $order);

            $columns = Db::name('cms_column')->where(['model' => $model['id']])->column('id,name');

            // 使用ZBuilder快速创建数据表格
            return ZBuilder::make('table')
                ->setSearch(['title' => '标题', 'cms_column.name' => '栏目名称']) // 设置搜索框
                ->addColumns([ // 批量添加数据列
                    ['id', 'ID'],
                    ['title', '标题'],
                    ['cid', '栏目名称', 'select', $columns],
                    ['view', '点击量'],
                    ['username', '发布人'],
                    ['update_time', '更新时间', 'datetime'],
                    ['sort', '排序', 'text.edit'],
                    ['status', '状态', 'switch'],
                    ['right_button', '操作', 'btn']
                ])
                ->setTableName('cms_document')
                ->addTopButton('add', ['href' => url('document/add', ['model' => $model['id']])]) // 添加顶部按钮
                ->addTopButton('enable', ['href' => url('document/enable', ['table' => 'cms_document'])]) // 添加顶部按钮
                ->addTopButton('disable', ['href' => url('document/disable', ['table' => 'cms_document'])]) // 添加顶部按钮
                ->addTopButton('delete', ['href' => url('document/delete', ['table' => 'cms_document'])]) // 添加顶部按钮
                ->addRightButton('edit', ['href' => url('document/edit', ['id' => '__id__'])]) // 添加右侧按钮
                ->addRightButton('delete', ['href' => url('document/delete', ['ids' => '__id__', 'table' => 'cms_document'])]) // 添加右侧按钮
                ->addOrder('id,title,cid,view,username,update_time')
                ->addFilter('cid', $columns)
                ->addFilter(['username' => 'admin_user'])
                ->addFilterMap(['cid' => ['model' => $model['id']]])
                ->setRowList($data_list) // 设置表格数据
                ->fetch(); // 渲染模板
        }
    }
    
    public function yl_messagebord()
    {
    	cookie('__forward__', $_SERVER['REQUEST_URI']);
        $model_name = 'yl_member';
        $model      = Db::name('cms_model')->where('name', $model_name)->find();
        // 获取查询条件
        $map = $this->getMap();
        $order = $this->getOrder();
        // 数据列表
		$data_list = Db::name('messagebord')->alias('a')->join('member b', 'a.member_id=b.id','LEFT')->where($map)->field('a.*,b.title as bname,b.id as bid')->order($order)->paginate();
       
        // 分页数据
        $page = $data_list->render();
       
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
                        ->setPageTitle('留言板') // 设置页面标题
                        ->setTableName('messagebord') // 设置数据表名
                        ->setSearch(['message' => '留言内容']) // 设置搜索参数
                        ->addColumns([ // 批量添加列
                            ['id', 'ID'],
                            //['bname', '会员名','detail',['model' => $model['id']],'_blank','','bid'],
                            ['bname', '会员名', 'link', url('cms/document/detail', ['id'=>'__bid__','model' => $model['id']])],
                            ['create_time', '操作时间','datetime'],
                            ['message', '留言内容','textarea'], 
                            ['sh', '是否前台显示','switch'],                          
                            ['right_button', '操作', 'btn']
                        ])
                        ->addOrder('status,create_time,id')
                        ->addRightButton('edit', ['href' => url('cms/document/edit', ['model' => $model['id'], 'id' => '__id__'])]) // 添加右侧按钮
//                        ->addTopButtons('enable,disable,delete') // 批量添加顶部按钮
//                        ->addRightButtons('edit') // 批量添加右侧按钮
                        ->addRightButtons('delete')// 删除数据
                        ->setRowList($data_list) // 设置表格数据
                        ->setPages($page) // 设置分页数据
                        ->fetch(); // 渲染页面
    }
    
     
    public function detail($id = null, $model = '') 
    {
        if ($id === null)
            $this->error ( '参数错误' );
        $thisid = $id;
        // 获取数据
        $info = DocumentModel::getOne ( $id, $model );
        // 独立模型只取该模型的字段，不包含系统字段
        if ($model != '') {
            $info ['model'] = $model;
            $where ['model'] = $model;
        } else {
            $where ['model'] = [
                'in',
                [
                    0,
                    $info ['model']
                ]
            ];
        }
        // 获取文档模型字段
        $where ['status'] = 1;
        $where ['show'] = 1;
        $fields = FieldModel::where ( $where )->order ( 'sort asc,id asc' )->column ( true );
        //dump($fields);
		foreach ( $fields as $id => &$value ) {
            // 解析options
            if ($value ['options'] != '') {
                $value ['options'] = parse_attr ( $value ['options'] );
            }
            // 日期时间
            switch ($value ['type']) {
                case 'date' :
                    $info [$value ['name']] = format_time ( $info [$value ['name']], 'Y-m-d' );
                    break;
                case 'time' :
                    $info [$value ['name']] = format_time ( $info [$value ['name']], 'H:i:s' );
                    break;
                case 'datetime' :
                    $info [$value ['name']] = empty ( $info [$value ['name']] ) ? '' : format_time ( $info [$value ['name']] );
                    break;
            }
        }
        // 获取相同内容模型的栏目
        $columns = Db::name ( 'cms_column' )->where ( [
            'model' => $where ['model']
        ] )->whereOr ( 'model', $info ['model'] )->order ( 'pid,id' )->column ( 'id,name,pid' );
        $columns = Tree::config ( [
            'title' => 'name'
        ] )->toList ( $columns, current ( $columns )['pid'] );
        $result = [ ];
        foreach ( $columns as $column ) {
            $result [$column ['id']] = $column ['title_display'];
        }
        $columns = $result;
        // 添加额外表单项信息
        $extra_field = [
            [
                'name' => 'id',
                'type' => 'hidden'
            ],
            [
                'name' => 'model',
                'type' => 'hidden'
            ]
        ];
        $fields = array_merge ( $extra_field, $fields );
        $this->assign ( 'id', $thisid );
        $this->assign ( 'model', $model );
        // 使用ZBuilder快速创建表单
        return ZBuilder::make ( 'detail' )
        ->setFormItems ( $fields )
        ->setFormData ( $info )
        ->fetch ();
    }
    
    
}