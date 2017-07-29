# SimDI
一个简单的PHP依赖注入框架，支持autowire。

附上之前写的一篇文: [用PHP撸一个DI容器](https://justpoet.github.io/2017/07/16/%E7%94%A8PHP%E6%92%B8%E4%B8%80%E4%B8%AADI%E5%AE%B9%E5%99%A8/)

## 安装

```shell
composer require zean/sim-di
```

## 使用
1. 不使用面向接口编程风格
    
    假如有Car和Driver两个类，Driver依赖Car
    
    ```php
    class Car
    {
        protected $name = '汽车';
     
        public function getName()
        {
            return $this->name;
        }
    }
    ```
    
    ```php
    class Driver
    {
        protected $car;
     
        public function __construct(Car $car)
        {
            $this->car = $car;
        }
     
        public function drive()
        {
            return '驾驶' . $this->car->getName();
        }
    }
    ```
    
    当我们需要Diver实例的时候，这时候我们只需要让容器创建，容器会自动注入Car实例
    
    ```php
    $app = \SimDI\Container::getInstance();
    $driver = $app->get(Driver::class);
    echo $driver->drive();
    ```
    
> output:驾驶汽车
    
2. 使用面向接口编程风格

假如我们用面向接口的方式来，我稍微修改一下上面的代码：

```php
abstract class Car
{
    protected $name = '汽车';
    
    public function getName()
    {
        return $this->name;
    }
}

```
```php
interface Driveable
{
    public function run();
}
```
```php
class Benz extends Car implements Driveable
{
    protected $name = '奔驰';
    
    public function run()
    {
        return $this->getName() . '启动了！';
    }
}
```
```php
class Driver
{
    protected $car;
    
    public function __construct(Driveable $car)
    {
        $this->car = $car;
    }
    
    public function drive()
    {
        return '驾驶' . $this->car->run();
    }
}
```
面向接口编程时需要有一个配置来指定interface和实现类的对应关系，如下：

```php
$config = [
    Driveable::class => Benz::class,
];
```
然后在创建容器的时候我们使用上述配置：

```php
$app = \SimDI\Container::getInstance($config);
$driver = $app->get(Driver::class);
echo $driver->drive();
```
> output:驾驶奔驰启动了！