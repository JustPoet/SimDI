<?php
/**
 * AE86.php
 *
 * 作者: zhengzean (zhengzean01@gmail.com)
 * 创建日期: 2017/7/19 17:17
 * 修改记录:
 *
 * $Id$
 */

namespace SimDI\Tests;


class AE86 implements CarInterface
{
    protected $name = 'AE86';

    protected $manufacture;

    public function setManufacture($manufacture)
    {
        $this->manufacture = $manufacture;
    }

    public function run()
    {
        return $this->manufacture . ':' . $this->name;
    }
}