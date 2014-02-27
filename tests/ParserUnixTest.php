<?php

class ParserUnixTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Cannot match expression
	 */
	public function testFailsOnBadFormat()
	{
		$parser = new \Krolikoff\CronGen\Parser\ParserUnix();
		$parser->convertConfig('Some bad string');
	}
	
	public function testConvertForEveryXMinutes()
	{
		$parser = new \Krolikoff\CronGen\Parser\ParserUnix();
		$this->assertEquals('*/5 * * * *', $parser->convertConfig('every 5 minutes'));
	}

	public function testConvertForEveryHourAtXMinute()
	{
		$parser = new \Krolikoff\CronGen\Parser\ParserUnix();
		$this->assertEquals('12 * * * *', $parser->convertConfig('every 12th minute of every hour'));
	}
	
	public function testConvertForEveryDayAtX()
	{
		$parser = new \Krolikoff\CronGen\Parser\ParserUnix();
		$this->assertEquals('5 10 * * *', $parser->convertConfig('every day at 10:05'));
	}
	
	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Unsupported match type
	 */
	public function testUnsupportedFormatFails()
	{
		//add new constant to current regexps 
		$reflection = new \ReflectionProperty('\Krolikoff\CronGen\Parser\ParserUnix', 'regexps');
		$reflection->setAccessible(true);
		$current_regexps = $reflection->getValue(new \Krolikoff\CronGen\Parser\ParserUnix());
		$current_regexps[100001] = 'strange regexp';
		
		//inject new regexps
		$parser = new \Krolikoff\CronGen\Parser\ParserUnix();
		$reflection = new \ReflectionProperty('\Krolikoff\CronGen\Parser\ParserUnix', 'regexps');
		$reflection->setAccessible(true);
		$reflection->setValue($parser, $current_regexps);

		//should throw exception
		$parser->convertConfig('strange regexp');
	}
	
	public function testGetRunConfig()
	{
		$parser = new \Krolikoff\CronGen\Parser\ParserUnix();
		$this->assertEquals("#title\n*/10 * * * * /bin/cmd", $parser->getRunConfig(
			'every 10 minutes',
			'/bin/cmd',
			'title'
		));
	}
}
