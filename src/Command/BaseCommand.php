<?php


namespace App\Command;


use Symfony\Component\Console\Command\Command;

class BaseCommand extends Command
{
    /**
     * 將駝峰 ClassName 轉為小寫下底線
     * @param $str
     * @return string
     */
    protected function toUnderScore($str)
    {
        $dstr = preg_replace_callback('/([A-Z]+)/',function($matchs) {
            return '_'.strtolower($matchs[0]);
        }, $str);
        return trim(preg_replace('/_{2,}/','_',$dstr),'_');
    }
}