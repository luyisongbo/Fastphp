<?php
namespace fastphp;

//框架根目录
define('CORE_PATH') or define('CORE_PATH',__DIR__);

//Fastphp框架核心
class Fastphp{

    //配置内容
    protected $config = [];

    public function  __construct($config){
        $this->config = $config;
    }

    //运行程序
    public function run(){
        //var_dump($this->config);
        $this->route();
    }

    //路由处理 禁止？后url参数
    public function route(){
        $controllerName = $this->config['defaultController'];
        $actionName = $this->config['defaultAction'];
        $param = array();

        //URL处理
        $url = $_SERVER['REQUEST_URI'];
        //清除？之后的内容
        $position = strpos($url,'?');
        $url = $position === false ? $url : substr($url, 0, $position);
        //清除前后 '/'
        $url = trim($url,'/');

        if($url){
            $urlArray = explode('/',$url);
            //删除空数组元素
            $urlArray = array_filter($urlArray);
            //获取控制器名、方法名和url参数
            $controllerName = ucfirst($urlArray[1]);
            array_shift($urlArray);
            $actionName = $urlArray ? $urlArray[1] : $actionName;
            array_shift($urlArray);
            $param = $urlArray ? $urlArray : array();
        }

        //判断控制器和方法是否存在
        $controller = 'app\\controllers\\'.$controllerName.'Controller';
        if(!class_exists($controller)){
            exit($controller . '控制器不存在！');
        }
        if(!method_exists($controller,$actionName)){
            exit($actionName . '方法不存在！');
        }

        // 如果控制器和操作名存在，则实例化控制器，因为控制器对象里面
        // 还会用到控制器名和操作名，所以实例化的时候把他们俩的名称也
        // 传进去。结合Controller基类一起看
        $dispatch = new $controller($controllerName,$actionName);

        // $dispatch保存控制器实例化后的对象，我们就可以调用它的方法，
        // 也可以像方法中传入参数，以下等同于：$dispatch->$actionName($param)
        call_user_func_array(array($dispatch,$actionName),$param);
    }



}