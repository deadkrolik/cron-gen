<?php namespace Krolikoff\CronGen\Installer;

interface Installer
{
	public function install($lines);
	
	public function clear();
}
