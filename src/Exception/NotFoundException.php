<?php
/**
 * NotFoundException.php
 *
 * 作者: zhengzean (zhengzean01@gmail.com)
 * 创建日期: 2017/7/18 11:27
 * 修改记录:
 *
 * $Id$
 */

namespace SimDI\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{

}