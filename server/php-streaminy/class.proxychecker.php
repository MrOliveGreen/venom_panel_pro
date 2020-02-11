<?php


namespace IP2Proxy;

class Database
{
	const VERSION = '1.1.2';
	const FIELD_NOT_SUPPORTED = 'NOT SUPPORTED';
	const FIELD_NOT_KNOWN = 'This parameter does not exists. Please verify.';
	const INVALID_IP_ADDRESS = 'INVALID IP ADDRESS';
	const COUNTRY_CODE = 1;
	const COUNTRY_NAME = 2;
	const REGION_NAME = 3;
	const CITY_NAME = 4;
	const ISP = 5;
	const IS_PROXY = 6;
	const PROXY_TYPE = 7;
	const COUNTRY = 101;
	const ALL = 1001;
	const IP_ADDRESS = 1002;
	const IP_VERSION = 1003;
	const IP_NUMBER = 1004;
	const EXCEPTION = 10000;
	const EXCEPTION_NO_SHMOP = 10001;
	const EXCEPTION_SHMOP_READING_FAILED = 10002;
	const EXCEPTION_SHMOP_WRITING_FAILED = 10003;
	const EXCEPTION_SHMOP_CREATE_FAILED = 10004;
	const EXCEPTION_DBFILE_NOT_FOUND = 10005;
	const EXCEPTION_NO_MEMORY = 10006;
	const EXCEPTION_NO_CANDIDATES = 10007;
	const EXCEPTION_FILE_OPEN_FAILED = 10008;
	const EXCEPTION_NO_PATH = 10009;
	const EXCEPTION_BCMATH_NOT_INSTALLED = 10010;
	const FILE_IO = 100001;
	const MEMORY_CACHE = 100002;
	const SHARED_MEMORY = 100003;
	const SHM_PERMS = 384;
	const SHM_CHUNK_SIZE = 524288;

	static private $columns = [self::COUNTRY_CODE => [0 => 8, 1 => 12, 2 => 12, 3 => 12], self::COUNTRY_NAME => [0 => 8, 1 => 12, 2 => 12, 3 => 12], self::REGION_NAME => [0 => 0, 1 => 0, 2 => 16, 3 => 16], self::CITY_NAME => [0 => 0, 1 => 0, 2 => 20, 3 => 20], self::ISP => [0 => 0, 1 => 0, 2 => 0, 3 => 24], self::PROXY_TYPE => [0 => 0, 1 => 8, 2 => 8, 3 => 8]];
	static private $names = [self::COUNTRY_CODE => 'countryCode', self::COUNTRY_NAME => 'countryName', self::REGION_NAME => 'regionName', self::CITY_NAME => 'cityName', self::ISP => 'isp', self::IS_PROXY => 'isProxy', self::PROXY_TYPE => 'proxyType', self::IP_ADDRESS => 'ipAddress', self::IP_VERSION => 'ipVersion', self::IP_NUMBER => 'ipNumber'];
	static private $databases = ['IP2PROXY-IP-PROXYTYPE-COUNTRY-REGION-CITY-ISP', 'IP2PROXY-IP-PROXYTYPE-COUNTRY-REGION-CITY', 'IP2PROXY-IP-PROXYTYPE-COUNTRY', 'IP2PROXY-IP-COUNTRY', 'IPV6-PROXYTYPE-COUNTRY-REGION-CITY-ISP', 'IPV6-PROXYTYPE-COUNTRY-REGION-CITY', 'IPV6-PROXYTYPE-COUNTRY', 'IPV6-COUNTRY'];
	static private $buffer = [];
	static private $floatSize;
	static private $memoryLimit;
	private $mode;
	private $resource = false;
	private $date;
	private $type;
	private $columnWidth = [];
	private $offset = [];
	private $ipCount = [];
	private $ipBase = [];
	private $indexBaseAddr = [];
	private $year;
	private $month;
	private $day;
	private $defaultFields = \self::ALL;

	public function __construct()
	{
	}

	public function __destruct()
	{
	}

	public function open($file = NULL, $mode = \self::FILE_IO, $defaultFields = \self::ALL)
	{
		if (!function_exists('bcadd')) {
			throw new \Exception('IP2Proxy\\Database: BCMath extension is not installed.', self::EXCEPTION_BCMATH_NOT_INSTALLED);
		}

		$rfile = self::findFile($file);
		$size = filesize($rfile);

		switch ($mode) {
		case self::SHARED_MEMORY:
			if (!extension_loaded('shmop')) {
				throw new \Exception('IP2Proxy\\Database: Please make sure your PHP setup has the \'shmop\' extension enabled.', self::EXCEPTION_NO_SHMOP);
			}

			$limit = self::getMemoryLimit();
			if ((false !== $limit) && ($limit < $size)) {
				throw new \Exception(__CLASS__ . ': Insufficient memory to load file \'' . $rfile . '\'.', self::EXCEPTION_NO_MEMORY);
			}

			$this->mode = self::SHARED_MEMORY;
			$shmKey = self::getShmKey($rfile);
			$this->resource = @shmop_open($shmKey, 'a', 0, 0);

			if (false === $this->resource) {
				$fp = fopen($rfile, 'rb');

				if (false === $fp) {
					throw new \Exception(__CLASS__ . ': Unable to open file \'' . $rfile . '\'.', self::EXCEPTION_FILE_OPEN_FAILED);
				}

				$shmId = @shmop_open($shmKey, 'n', self::SHM_PERMS, $size);

				if (false === $shmId) {
					throw new \Exception(__CLASS__ . ': Unable to create shared memory block \'' . $shmKey . '\'.', self::EXCEPTION_SHMOP_CREATE_FAILED);
				}

				$pointer = 0;

				while ($pointer < $size) {
					$buf = fread($fp, self::SHM_CHUNK_SIZE);
					shmop_write($shmId, $buf, $pointer);
					$pointer += self::SHM_CHUNK_SIZE;
				}

				shmop_close($shmId);
				fclose($fp);
				$this->resource = @shmop_open($shmKey, 'a', 0, 0);

				if (false === $this->resource) {
					throw new \Exception(__CLASS__ . ': Unable to access shared memory block \'' . $shmKey . '\' for reading.', self::EXCEPTION_SHMOP_READING_FAILED);
				}
			}

			break;
		case self::FILE_IO:
			$this->mode = self::FILE_IO;
			$this->resource = @fopen($rfile, 'rb');

			if (false === $this->resource) {
				throw new \Exception(__CLASS__ . ': Unable to open file \'' . $rfile . '\'.', self::EXCEPTION_FILE_OPEN_FAILED);
			}

			break;
		case self::MEMORY_CACHE:
			$this->mode = self::MEMORY_CACHE;
			$this->resource = $rfile;

			if (!array_key_exists($rfile, self::$buffer)) {
				$limit = self::getMemoryLimit();
				if ((false !== $limit) && ($limit < $size)) {
					throw new \Exception(__CLASS__ . ': Insufficient memory to load file \'' . $rfile . '\'.', self::EXCEPTION_NO_MEMORY);
				}

				self::$buffer[$rfile] = @file_get_contents($rfile);

				if (false === self::$buffer[$rfile]) {
					throw new \Exception(__CLASS__ . ': Unable to open file \'' . $rfile . '\'.', self::EXCEPTION_FILE_OPEN_FAILED);
				}
			}

			break;
		}

		if (NULL === self::$floatSize) {
			self::$floatSize = strlen(pack('f', M_PI));
		}

		$this->defaultFields = $defaultFields;
		$this->type = $this->readByte(1) - 1;
		$this->columnWidth[4] = $this->readByte(2) * 4;
		$this->columnWidth[6] = $this->columnWidth[4] + 12;
		$this->offset[4] = -4;
		$this->offset[6] = 8;
		$this->year = 2000 + $this->readByte(3);
		$this->month = $this->readByte(4);
		$this->day = $this->readByte(5);
		$this->date = date('Y-m-d', strtotime($this->year . '-' . $this->month . '-' . $this->day));
		$this->ipCount[4] = $this->readWord(6);
		$this->ipBase[4] = $this->readWord(10);
		$this->ipCount[6] = $this->readWord(14);
		$this->ipBase[6] = $this->readWord(18);
		$this->indexBaseAddr[4] = $this->readWord(22);
		$this->indexBaseAddr[6] = $this->readWord(26);
	}

	public function close()
	{
		switch ($this->mode) {
		case self::FILE_IO:
			if (false !== $this->resource) {
				fclose($this->resource);
				$this->resource = false;
			}

			break;
		case self::SHARED_MEMORY:
			if (false !== $this->resource) {
				shmop_close($this->resource);
				$this->resource = false;
			}

			break;
		}
	}

	protected function shmTeardown($file)
	{
		if (!extension_loaded('shmop')) {
			throw new \Exception('IP2Proxy\\Database: Please make sure your PHP setup has the \'shmop\' extension enabled.', self::EXCEPTION_NO_SHMOP);
		}

		$rfile = realpath($file);

		if (false === $rfile) {
			throw new \Exception(__CLASS__ . ': Database file \'' . $file . '\' does not seem to exist.', self::EXCEPTION_DBFILE_NOT_FOUND);
		}

		$shmKey = self::getShmKey($rfile);
		$shmId = @shmop_open($shmKey, 'w', 0, 0);

		if (false === $shmId) {
			throw new \Exception(__CLASS__ . ': Unable to access shared memory block \'' . $shmKey . '\' for writing.', self::EXCEPTION_SHMOP_WRITING_FAILED);
		}

		shmop_delete($shmId);
		shmop_close($shmId);
	}

	static private function getMemoryLimit()
	{
		if (NULL === self::$memoryLimit) {
			$limit = ini_get('memory_limit');

			if ('' === (string) $limit) {
				$limit = '128M';
			}

			$value = (int) $limit;

			if ($value < 0) {
				$value = false;
			}
			else {
				switch (strtoupper(substr($limit, -1))) {
				case 'G':
					$value *= 1024;
				case 'M':
					$value *= 1024;
				case 'K':
					$value *= 1024;
				}
			}

			self::$memoryLimit = $value;
		}

		return self::$memoryLimit;
	}

	static private function findFile($file = NULL)
	{
		if (NULL !== $file) {
			$rfile = realpath($file);

			if (false === $rfile) {
				throw new \Exception(__CLASS__ . ': Database file \'' . $file . '\' does not seem to exist.', self::EXCEPTION_DBFILE_NOT_FOUND);
			}

			return $rfile;
		}
		else {
			$current = realpath(dirname(__FILE__));

			if (false === $current) {
				throw new \Exception('IP2Proxy\\Database: Cannot determine current path.', self::EXCEPTION_NO_PATH);
			}

			foreach (self::$databases as $database) {
				$rfile = realpath($current . '/' . $database . '.BIN');

				if (false !== $rfile) {
					return $rfile;
				}
			}

			throw new \Exception('IP2Proxy\\Database: No candidate database files found.', self::EXCEPTION_NO_CANDIDATES);
		}
	}

	static private function wrap8($x)
	{
		return $x + ($x < 0 ? 256 : 0);
	}

	static private function wrap32($x)
	{
		return $x + ($x < 0 ? 4294967296.0 : 0);
	}

	static private function getShmKey($filename)
	{
		return (int) sprintf('%u', self::wrap32(crc32(__FILE__ . ':' . $filename)));
	}

	static private function ipBetween($version, $ip, $low, $high)
	{
		if (4 === $version) {
			if ($low <= $ip) {
				if ($ip < $high) {
					return 0;
				}
				else {
					return 1;
				}
			}
			else {
				return -1;
			}
		}
		else if (bccomp($low, $ip, 0) <= 0) {
			if (bccomp($ip, $high, 0) <= -1) {
				return 0;
			}
			else {
				return 1;
			}
		}
		else {
			return -1;
		}
	}

	static private function ipVersionAndNumber($ip)
	{
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			return [4, sprintf('%u', ip2long($ip))];
		}
		else if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			$result = 0;

			foreach (str_split(bin2hex(inet_pton($ip)), 8) as $word) {
				$result = bcadd(bcmul($result, '4294967296', 0), self::wrap32(hexdec($word)), 0);
			}

			return [6, $result];
		}
		else {
			return [false, false];
		}
	}

	static private function bcBin2Dec($data)
	{
		$parts = [unpack('V', substr($data, 12, 4)), unpack('V', substr($data, 8, 4)), unpack('V', substr($data, 4, 4)), unpack('V', substr($data, 0, 4))];

		foreach ($parts as &$part) {
			if ($part[1] < 0) {
				$part[1] += 4294967296.0;
			}
		}

		$result = bcadd(bcadd(bcmul($parts[0][1], bcpow(4294967296.0, 3)), bcmul($parts[1][1], bcpow(4294967296.0, 2))), bcadd(bcmul($parts[2][1], 4294967296.0), $parts[3][1]));
		return $result;
	}

	private function read($pos, $len)
	{
		switch ($this->mode) {
		case self::SHARED_MEMORY:
			return shmop_read($this->resource, $pos, $len);
		case self::MEMORY_CACHE:
			return $data = substr(self::$buffer[$this->resource], $pos, $len);
		}

		fseek($this->resource, $pos, SEEK_SET);
		return fread($this->resource, $len);
	}

	private function readString($pos, $additional = 0)
	{
		$spos = $this->readWord($pos) + $additional;
		return $this->read($spos + 1, $this->readByte($spos + 1));
	}

	private function readFloat($pos)
	{
		return unpack('f', $this->read($pos - 1, self::$floatSize))[1];
	}

	private function readQuad($pos)
	{
		return self::bcBin2Dec($this->read($pos - 1, 16));
	}

	private function readWord($pos)
	{
		return self::wrap32(unpack('V', $this->read($pos - 1, 4))[1]);
	}

	private function readByte($pos)
	{
		return self::wrap8(unpack('C', $this->read($pos - 1, 1))[1]);
	}

	private function readCountryNameAndCode($pointer)
	{
		if (false === $pointer) {
			$countryCode = self::INVALID_IP_ADDRESS;
			$countryName = self::INVALID_IP_ADDRESS;
		}
		else if (0 === self::$columns[self::COUNTRY_CODE][$this->type]) {
			$countryCode = self::FIELD_NOT_SUPPORTED;
			$countryName = self::FIELD_NOT_SUPPORTED;
		}
		else {
			$countryCode = $this->readString($pointer + self::$columns[self::COUNTRY_CODE][$this->type]);
			$countryName = $this->readString($pointer + self::$columns[self::COUNTRY_NAME][$this->type], 3);
		}

		return [$countryName, $countryCode];
	}

	private function readRegionName($pointer)
	{
		if (false === $pointer) {
			$regionName = self::INVALID_IP_ADDRESS;
		}
		else if (0 === self::$columns[self::REGION_NAME][$this->type]) {
			$regionName = self::FIELD_NOT_SUPPORTED;
		}
		else {
			$regionName = $this->readString($pointer + self::$columns[self::REGION_NAME][$this->type]);
		}

		return $regionName;
	}

	private function readCityName($pointer)
	{
		if (false === $pointer) {
			$cityName = self::INVALID_IP_ADDRESS;
		}
		else if (0 === self::$columns[self::CITY_NAME][$this->type]) {
			$cityName = self::FIELD_NOT_SUPPORTED;
		}
		else {
			$cityName = $this->readString($pointer + self::$columns[self::CITY_NAME][$this->type]);
		}

		return $cityName;
	}

	private function readIsp($pointer)
	{
		if (false === $pointer) {
			$isp = self::INVALID_IP_ADDRESS;
		}
		else if (0 === self::$columns[self::ISP][$this->type]) {
			$isp = self::FIELD_NOT_SUPPORTED;
		}
		else {
			$isp = $this->readString($pointer + self::$columns[self::ISP][$this->type]);
		}

		return $isp;
	}

	private function readProxyType($pointer)
	{
		if (false === $pointer) {
			$proxyType = self::INVALID_IP_ADDRESS;
		}
		else if (0 === self::$columns[self::PROXY_TYPE][$this->type]) {
			$proxyType = self::FIELD_NOT_SUPPORTED;
		}
		else {
			$proxyType = $this->readString($pointer + self::$columns[self::PROXY_TYPE][$this->type]);
		}

		return $proxyType;
	}

	private function readIp($version, $pos)
	{
		if (4 === $version) {
			return self::wrap32($this->readWord($pos));
		}
		else if (6 === $version) {
			return $this->readQuad($pos);
		}
		else {
			return false;
		}
	}

	private function binSearch($version, $ipNumber)
	{
		if (false === $version) {
			return false;
		}

		$base = $this->ipBase[$version];
		$offset = $this->offset[$version];
		$width = $this->columnWidth[$version];
		$high = $this->ipCount[$version];
		$low = 0;
		$indexBaseStart = $this->indexBaseAddr[$version];

		if (0 < $indexBaseStart) {
			$indexPos = 0;

			switch ($version) {
			case 4:
				$ipNum1_2 = intval($ipNumber / 65536);
				$indexPos = $indexBaseStart + ($ipNum1_2 << 3);
				break;
			case 6:
				$ipNum1 = intval(bcdiv($ipNumber, bcpow('2', '112')));
				$indexPos = $indexBaseStart + ($ipNum1 << 3);
				break;
			default:
				return false;
			}

			$low = $this->readWord($indexPos);
			$high = $this->readWord($indexPos + 4);
		}

		while ($low <= $high) {
			$mid = (int) $low + (($high - $low) >> 1);
			$ip_from = $this->readIp($version, $base + ($width * $mid));
			$ip_to = $this->readIp($version, $base + ($width * ($mid + 1)));

			switch (self::ipBetween($version, $ipNumber, $ip_from, $ip_to)) {
			case 0:
				return $base + $offset + ($mid * $width);
			case -1:
				$high = $mid - 1;
				break;
			case 1:
				$low = $mid + 1;
				break;
			}
		}

		return false;
	}

	public function getPackageVersion()
	{
		return $this->type + 1;
	}

	protected function getFields($asNames = false)
	{
		$result = array_keys(array_filter(self::$columns, function($field) {
			return 0 !== $field[$this->type];
		}));

		if ($asNames) {
			$return = [];

			foreach ($result as $field) {
				$return[] = self::$names[$field];
			}

			return $return;
		}
		else {
			return $result;
		}
	}

	public function getModuleVersion()
	{
		return self::VERSION;
	}

	public function getDatabaseVersion()
	{
		return $this->year . '.' . $this->month . '.' . $this->day;
	}

	public function isProxy($ip)
	{
		return self::lookup($ip, self::IS_PROXY);
	}

	public function getCountryShort($ip)
	{
		return self::lookup($ip, self::COUNTRY_CODE);
	}

	public function getCountryLong($ip)
	{
		return self::lookup($ip, self::COUNTRY_NAME);
	}

	public function getRegion($ip)
	{
		return self::lookup($ip, self::REGION_NAME);
	}

	public function getCity($ip)
	{
		return self::lookup($ip, self::CITY_NAME);
	}

	public function getISP($ip)
	{
		return self::lookup($ip, self::ISP);
	}

	public function getProxyType($ip)
	{
		return self::lookup($ip, self::PROXY_TYPE);
	}

	public function getAll($ip)
	{
		return self::lookup($ip, self::ALL);
	}

	protected function lookup($ip, $fields = NULL, $asNamed = true)
	{
		list($ipVersion, $ipNumber) = self::ipVersionAndNumber($ip);
		$pointer = $this->binSearch($ipVersion, $ipNumber);

		if (NULL === $fields) {
			$fields = $this->defaultFields;
		}

		$ifields = (array) $fields;

		if (in_array(self::ALL, $ifields)) {
			$ifields[] = self::REGION_NAME;
			$ifields[] = self::CITY_NAME;
			$ifields[] = self::ISP;
			$ifields[] = self::IS_PROXY;
			$ifields[] = self::PROXY_TYPE;
			$ifields[] = self::COUNTRY;
			$ifields[] = self::IP_ADDRESS;
			$ifields[] = self::IP_VERSION;
			$ifields[] = self::IP_NUMBER;
		}

		$afields = array_keys(array_flip($ifields));
		rsort($afields);
		$done = [self::COUNTRY_CODE => false, self::COUNTRY_NAME => false, self::REGION_NAME => false, self::CITY_NAME => false, self::ISP => false, self::IS_PROXY => false, self::PROXY_TYPE => false, self::COUNTRY => false, self::IP_ADDRESS => false, self::IP_VERSION => false, self::IP_NUMBER => false];
		$results = [];

		foreach ($afields as $afield) {
			switch ($afield) {
			case self::ALL:
				break;
			case self::COUNTRY:
				if (!$done[self::COUNTRY]) {
					$results[self::COUNTRY_NAME] = $this->readCountryNameAndCode($pointer)[0];
					$results[self::COUNTRY_CODE] = $this->readCountryNameAndCode($pointer)[1];
					$done[self::COUNTRY] = true;
					$done[self::COUNTRY_CODE] = true;
					$done[self::COUNTRY_NAME] = true;
				}

				break;
			case self::COUNTRY_CODE:
				if (!$done[self::COUNTRY_CODE]) {
					$results[self::COUNTRY_CODE] = $this->readCountryNameAndCode($pointer)[1];
					$done[self::COUNTRY_CODE] = true;
				}

				break;
			case self::COUNTRY_NAME:
				if (!$done[self::COUNTRY_CODE]) {
					$results[self::COUNTRY_CODE] = $this->readCountryNameAndCode($pointer)[0];
					$done[self::COUNTRY_CODE] = true;
				}

				break;
			case self::REGION_NAME:
				if (!$done[self::REGION_NAME]) {
					$results[self::REGION_NAME] = $this->readRegionName($pointer);
					$done[self::REGION_NAME] = true;
				}

				break;
			case self::CITY_NAME:
				if (!$done[self::CITY_NAME]) {
					$results[self::CITY_NAME] = $this->readCityName($pointer);
					$done[self::CITY_NAME] = true;
				}

				break;
			case self::ISP:
				if (!$done[self::ISP]) {
					$results[self::ISP] = $this->readIsp($pointer);
					$done[self::ISP] = true;
				}

				break;
			case self::PROXY_TYPE:
				if (!$done[self::PROXY_TYPE]) {
					$results[self::PROXY_TYPE] = $this->readProxyType($pointer);
					$done[self::PROXY_TYPE] = true;
				}

				break;
			case self::IS_PROXY:
				if (!$done[self::IS_PROXY]) {
					if ($this->type == 0) {
						$countryCode = $this->readCountryNameAndCode($pointer)[1];
						$results[self::IS_PROXY] = ($countryCode == '-' ? 0 : 1);

						if (2 < strlen($countryCode)) {
							$results[self::IS_PROXY] = -1;
						}
					}
					else {
						$proxyType = $this->readProxyType($pointer);
						$results[self::IS_PROXY] = ($proxyType == '-' ? 0 : ($proxyType == 'DCH' ? 2 : 1));

						if (3 < strlen($proxyType)) {
							$results[self::IS_PROXY] = -1;
						}
					}

					$done[self::IS_PROXY] = true;
				}

				break;
			case self::IP_ADDRESS:
				if (!$done[self::IP_ADDRESS]) {
					$results[self::IP_ADDRESS] = $ip;
					$done[self::IP_ADDRESS] = true;
				}

				break;
			case self::IP_VERSION:
				if (!$done[self::IP_VERSION]) {
					$results[self::IP_VERSION] = $ipVersion;
					$done[self::IP_VERSION] = true;
				}

				break;
			case self::IP_NUMBER:
				if (!$done[self::IP_NUMBER]) {
					$results[self::IP_NUMBER] = $ipNumber;
					$done[self::IP_NUMBER] = true;
				}

				break;
			default:
				$results[$afield] = self::FIELD_NOT_KNOWN;
			}
		}
		if (is_array($fields) || (1 < count($results))) {
			if ($asNamed) {
				$return = [];

				foreach ($results as $key => $val) {
					if (array_key_exists($key, static::$names)) {
						$return[static::$names[$key]] = $val;
					}
					else {
						$return[$key] = $val;
					}
				}

				return $return;
			}
			else {
				return $results;
			}
		}
		else {
			return array_values($results)[0];
		}
	}
}

?>