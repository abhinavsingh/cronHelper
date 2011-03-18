<?php
/** 
 * cronHelper - Utility script to avoid cron job overlap
 *
 * Copyright (c) 2009-2010, Abhinav Singh <me@abhinavsingh.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Abhinav Singh nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRIC
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 * 
 * @package cronHelper
 * @subpackage core
 * @author Abhinav Singh <me@abhinavsingh.com>
 * @copyright Abhinav Singh
 * @link http://abhinavsingh.com/blog/2009/12/how-to-use-locks-in-php-cron-jobs-to-avoid-cron-overlaps/
 */
	
	require_once 'cronHelper.ini';
	
	/**
	 * cronHelper is helpful in avoiding cron job overlaps
	 * when a previous running cron job has yet not finished
	 * and next cron job has been triggered
	 */
	class cronHelper {
	
		private static $pid;

		function __construct() {}

		function __clone() {}

		private static function isrunning() {
			$pids = explode(PHP_EOL, `ps -e | awk '{print $1}'`);
			if(in_array(self::$pid, $pids)) return TRUE;
			return FALSE;
		}

		public static function lock() {
			global $argv;
			$lock_file = LOCK_DIR.$argv[0].LOCK_SUFFIX;
			
			if(file_exists($lock_file)) {
				self::$pid = file_get_contents($lock_file);
				if(self::isrunning()) {
					error_log("==".self::$pid."== Already in progress...");
					return FALSE;
				}
				else {
					error_log("==".self::$pid."== Previous job died abruptly...");
				}
			}

			self::$pid = getmypid();
			file_put_contents($lock_file, self::$pid);
			error_log("==".self::$pid."== Lock acquired, processing the job...");
			return self::$pid;
		}

		public static function unlock() {
			global $argv;
			$lock_file = LOCK_DIR.$argv[0].LOCK_SUFFIX;
			if(file_exists($lock_file)) unlink($lock_file);
			error_log("==".self::$pid."== Releasing lock...");
			return TRUE;
		}

	}

?>