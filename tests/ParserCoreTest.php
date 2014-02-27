<?php

class ParserCoreTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Cannot match expression
	 */
	public function testFailsWhenCannotMatchExpression()
	{
		$parser = new \Krolikoff\CronGen\Parser\Parser();
		$parser->matchRegexp(']]]]]]]**[^%');
	}
	
	public function testMatchEveryXMinutes()
	{
		$parser = new \Krolikoff\CronGen\Parser\Parser();
		
		$const = $parser->matchRegexp('every 11 minutes');
		$this->assertEquals(\Krolikoff\CronGen\Parser\ParserUnix::FORMAT_EVERY_X_MINUTES, $const);
	}
	
	public function testMatchEveryHourAtXMinute()
	{
		$parser = new \Krolikoff\CronGen\Parser\Parser();
		
		$const = $parser->matchRegexp('every 11th minute of every hour');
		$this->assertEquals(\Krolikoff\CronGen\Parser\ParserUnix::FORMAT_EVERY_HOUR_AT_X, $const);
	}
	
	public function testMatchEveryDayAtX()
	{
		$parser = new \Krolikoff\CronGen\Parser\Parser();
		
		$const = $parser->matchRegexp('every day at 10:10');
		$this->assertEquals(\Krolikoff\CronGen\Parser\ParserUnix::FORMAT_EVERY_DAY_AT_X, $const);
	}
	
	public function testCaseInsensivitiveAndAdditionalSpacesMatch()
	{
		$parser = new \Krolikoff\CronGen\Parser\Parser();
		
		$const = $parser->matchRegexp('EvErY   1 minute ');
		$this->assertEquals(\Krolikoff\CronGen\Parser\ParserUnix::FORMAT_EVERY_X_MINUTES, $const);
	}
	
	public function testGetMatchesForEveryXMinutes()
	{
		$parser = new \Krolikoff\CronGen\Parser\Parser();
		$parser->matchRegexp('every 23 minutes');
		$this->assertEquals(array(23), $parser->getMatches());
	}
	
	public function testGetMatchesForEveryHourAtXMinute()
	{
		$parser = new \Krolikoff\CronGen\Parser\Parser();
        $parser->matchRegexp('every 22th minute of every hour');
		$this->assertEquals(array(22, 'th'), $parser->getMatches());
	}
	
	public function testGetMatchesForEveryDayAtX()
	{
		$parser = new \Krolikoff\CronGen\Parser\Parser();
		$parser->matchRegexp('every day at 19:15');
		$this->assertEquals(array(19,15), $parser->getMatches());
	}
}
