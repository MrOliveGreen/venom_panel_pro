<?php


function encrypt($string, $key = 5)
{
	$result = '';
	$k = strlen($string);

	for ($i = 0; $i < $k; $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key)) - 1, 1);
		$char = chr(ord($char) + ord($keychar));
		$result .= $char;
	}

	return base64_encode($result);
}

function decrypt($string, $key = 5)
{
	$result = '';
	$string = base64_decode($string);
	$k = strlen($string);

	for ($i = 0; $i < $k; $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key)) - 1, 1);
		$char = chr(ord($char) - ord($keychar));
		$result .= $char;
	}

	return $result;
}

function set_cpu_usage()
{
	$usage = shell_exec('top -b -n2 | grep "Cpu(s)"|tail -n 1 | awk \'{print $2 + $4}\'');
	return $usage;
}

function set_ram_usage()
{
	foreach (file('/proc/meminfo') as $ri) {
		$m[strtok($ri, ':')] = strtok('');
	}

	return round(100 - ((((int) $m['MemFree'] + (int) $m['Buffers'] + (int) $m['Cached']) / (int) $m['MemTotal']) * 100));
}

function set_uptime()
{
	$ut = strtok(@exec('cat /proc/uptime'), '.');
	$days = sprintf('%2d', $ut / 86400);
	$hours = sprintf('%2d', ($ut % 86400) / 3600);
	$min = sprintf('%2d', ($ut % 86400 % 3600) / 60);
	$sec = sprintf('%2d', $ut % 86400 % 3600 % 60);
	$uptime = [$days, $hours, $min, $sec];

	if ($uptime[0] == 0) {
		if ($uptime[1] == 0) {
			if ($uptime[2] == 0) {
				$result = $uptime[3] . ' second(s)';
			}
			else {
				$result = $uptime[2] . ' minute(s)';
			}
		}
		else {
			$result = $uptime[1] . ' hour(s)';
		}
	}
	else {
		$result = $uptime[0] . ' day(s)';
	}

	return $result;
}

function set_transcoding_profile($profile_id, $stream_width, $stream_height, $adaptive_profile = false)
{
	global $db;
	$obf_DS4JGRgFLQ0THBECOyUNH1wmEzMVOAE = [$profile_id];
	$obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE = $db->query('SELECT * FROM cms_transcoding WHERE transcoding_id = ?', $obf_DS4JGRgFLQ0THBECOyUNH1wmEzMVOAE);
	$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE = '';

	if ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_method'] != 'own') {
		if ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo'] != '') {
			$obf_DRQcDQYOBigsPwM8DxsuEAgLKCECBgE = getimagesize(DOCROOT . 'image/' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo']);
			$img_width = $obf_DRQcDQYOBigsPwM8DxsuEAgLKCECBgE[0];
			$img_height = $obf_DRQcDQYOBigsPwM8DxsuEAgLKCECBgE[1];
			$obf_DQ0ODzxACSIIJiwtHwwBDT4CBDckMTI = explode('x', $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo_resolution']);
			$obf_DRwmIQE0DyM4MRkaNkAcGz8LFAIoNzI = explode('x', $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo_margin']);

			if (trim($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_resolution']) != '') {
				$obf_DQQbJSMQPwQ5ATIyNi4HCTQXAhIFEjI = explode('x', $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_resolution']);
				$obf_DTITFjcJBCQSHREpODUhWxcaGSsFPDI = $obf_DQQbJSMQPwQ5ATIyNi4HCTQXAhIFEjI[0];
				$obf_DTYoPhwHGltADAQaPxMwPTY4KwwwLwE = $obf_DQQbJSMQPwQ5ATIyNi4HCTQXAhIFEjI[1];
				$obf_DR43IixAPxAHPC44Egw9BFwCMAs4PxE = (int) $img_width * ($obf_DTITFjcJBCQSHREpODUhWxcaGSsFPDI / 1920) * ($obf_DQ0ODzxACSIIJiwtHwwBDT4CBDckMTI[0] / $img_width);
				$obf_DQEEHQIcITUJFC8dAw0bJRcTLiMxDiI = (int) $img_height * ($obf_DTYoPhwHGltADAQaPxMwPTY4KwwwLwE / 1080) * ($obf_DQ0ODzxACSIIJiwtHwwBDT4CBDckMTI[1] / $img_height);
			}
			else {
				$obf_DTITFjcJBCQSHREpODUhWxcaGSsFPDI = $stream_width;
				$obf_DTYoPhwHGltADAQaPxMwPTY4KwwwLwE = $stream_height;
				$obf_DR43IixAPxAHPC44Egw9BFwCMAs4PxE = (int) $img_width * ($obf_DTITFjcJBCQSHREpODUhWxcaGSsFPDI / 1920) * ($obf_DQ0ODzxACSIIJiwtHwwBDT4CBDckMTI[0] / $img_width);
				$obf_DQEEHQIcITUJFC8dAw0bJRcTLiMxDiI = (int) $img_height * ($obf_DTYoPhwHGltADAQaPxMwPTY4KwwwLwE / 1080) * ($obf_DQ0ODzxACSIIJiwtHwwBDT4CBDckMTI[1] / $img_height);
			}

			switch ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo_position']) {
			case 1:
				if (trim($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo_margin']) != '') {
					$overlay = $obf_DRwmIQE0DyM4MRkaNkAcGz8LFAIoNzI[0] . ':' . $obf_DRwmIQE0DyM4MRkaNkAcGz8LFAIoNzI[1];
				}
				else {
					$overlay = '15:15';
				}

				break;
			case 2:
				if (trim($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo_margin']) != '') {
					$overlay = 'main_w-overlay_w-' . $obf_DRwmIQE0DyM4MRkaNkAcGz8LFAIoNzI[1] . ':' . $obf_DRwmIQE0DyM4MRkaNkAcGz8LFAIoNzI[0];
				}
				else {
					$overlay = 'main_w-overlay_w-15:15';
				}

				break;
			case 3:
				if (trim($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo_margin']) != '') {
					$overlay = $obf_DRwmIQE0DyM4MRkaNkAcGz8LFAIoNzI[1] . ':main_h-overlay_h-' . $obf_DRwmIQE0DyM4MRkaNkAcGz8LFAIoNzI[0];
				}
				else {
					$overlay = '15:main_h-overlay_h-15';
				}

				break;
			case 4:
				if (trim($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo_margin']) != '') {
					$overlay = 'main_w-overlay_w-' . $obf_DRwmIQE0DyM4MRkaNkAcGz8LFAIoNzI[1] . ':main_h-overlay_h-' . $obf_DRwmIQE0DyM4MRkaNkAcGz8LFAIoNzI[0];
				}
				else {
					$overlay = 'main_w-overlay_w-15:main_h-overlay_h-15';
				}

				break;
			}

			switch ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_method']) {
			case 'cpu':
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -filter_complex "movie=filename=' . DOCROOT . 'image/' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo'] . ',overlay=' . $overlay . ',scale=w=' . $obf_DR43IixAPxAHPC44Egw9BFwCMAs4PxE . ':h=' . $obf_DQEEHQIcITUJFC8dAw0bJRcTLiMxDiI . '[logo]; [v:0][logo]overlay=' . $overlay . ',scale=w=' . $obf_DTITFjcJBCQSHREpODUhWxcaGSsFPDI . ':h=' . $obf_DTYoPhwHGltADAQaPxMwPTY4KwwwLwE . '[overlay];[overlay]split=outputs=1[map:v:0]" -map [map:v:0] ';
				break;
			case 'quicksync':
				if ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_resolution'] != '') {
					$obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE = explode('x', $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_resolution']);

					if ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE['transcoding_vframerate'] != 0) {
						$obf_DQFAPhcJFgIMMTUlCVwmCAEUAww9CAE = '[scale:0]; [scale:0]fps=fps=' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE['transcoding_vframerate'] . ',scale_qsv=w=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[0] . ':h=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[1] . '[map:v:0]';
					}
					else {
						$obf_DQFAPhcJFgIMMTUlCVwmCAEUAww9CAE = '[scale:0]; [scale:0]scale_qsv=w=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[0] . ':h=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[1] . '[map:v:0]';
					}
				}
				else {
					$obf_DQFAPhcJFgIMMTUlCVwmCAEUAww9CAE = '';
				}

				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -filter_complex "movie=filename=' . DOCROOT . 'image/' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo'] . '[logo]; [v:0]hwdownload,format=pix_fmts=nv12[format:0];[format:0][logo]overlay=' . $overlay . '[overlay]; [overlay]split=outputs=1[hwupload:0]; [hwupload:0]hwupload=extra_hw_frames=10' . $obf_DQFAPhcJFgIMMTUlCVwmCAEUAww9CAE . '" -map [map:v:0] ';
				break;
			case 'vaapi':
				if ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_resolution'] != '') {
					$obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE = explode('x', $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_resolution']);

					if ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE['transcoding_vframerate'] != 0) {
						$obf_DQFAPhcJFgIMMTUlCVwmCAEUAww9CAE = '[scale:0]; [scale:0]fps=fps=' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE['transcoding_vframerate'] . ',scale_vaapi=w=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[0] . ':h=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[1] . '[map:v:0]';
					}
					else {
						$obf_DQFAPhcJFgIMMTUlCVwmCAEUAww9CAE = '[scale:0]; [scale:0]scale_vaapi=w=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[0] . ':h=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[1] . '[map:v:0]';
					}
				}
				else {
					$obf_DQFAPhcJFgIMMTUlCVwmCAEUAww9CAE = '';
				}

				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -filter_complex "movie=filename=' . DOCROOT . 'image/' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo'] . '[logo]; [v:0]hwdownload,format=pix_fmts=nv12[format:0];[format:0][logo]overlay=' . $overlay . '[overlay]; [overlay]split=outputs=1[hwupload:0]; [hwupload:0]hwupload=extra_hw_frames=10' . $obf_DQFAPhcJFgIMMTUlCVwmCAEUAww9CAE . '" -map [map:v:0] ';
				break;
			case 'gpu':
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -filter_complex "movie=filename=' . DOCROOT . 'image/' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo'] . '[logo]; [v:0]hwdownload,format=nv12[format:0];[format:0][logo]overlay=' . $overlay . '[overlay]; [overlay]split=outputs=1[hwupload:0]; [hwupload:0]hwupload=extra_hw_frames=10[map:v:0]" -map [map:v:0] ';
				break;
			}
		}

		if ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vframerate'] != 0) {
			$obf_DRksLSs2XC0UXBMpPBoDCTQiLisjPzI = true;
		}
		else {
			$obf_DRksLSs2XC0UXBMpPBoDCTQiLisjPzI = false;
		}

		if ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_resolution'] != '') {
			$obf_DTAEGSUKGhwKOAYlGhgwGxg1FyYDCSI = true;
			$obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE = explode('x', $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_resolution']);
		}
		else {
			$obf_DTAEGSUKGhwKOAYlGhgwGxg1FyYDCSI = false;
		}
		if (($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo'] != '') && ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_method'] == 'gpu')) {
			$obf_DQEyPz02Ej8BJA07BTkaMQkdMzQ3LTI = ' -map "0:a:0"';
		}
		else if (($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo'] != '') && ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_method'] == 'quicksync')) {
			$obf_DQEyPz02Ej8BJA07BTkaMQkdMzQ3LTI = ' -map "0:a:0"';
		}
		else if (($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo'] != '') && ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_method'] == 'vaapi')) {
			$obf_DQEyPz02Ej8BJA07BTkaMQkdMzQ3LTI = ' -map "0:a:0"';
		}
		else if (($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo'] != '') && ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_method'] == 'cpu')) {
			$obf_DQEyPz02Ej8BJA07BTkaMQkdMzQ3LTI = ' -map "0:a:0"';
		}
		else {
			$obf_DQEyPz02Ej8BJA07BTkaMQkdMzQ3LTI = ' -map "0:a:0"';
		}

		switch ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_method']) {
		case 'cpu':
			if (($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo'] == '') && !$adaptive_profile && ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_mapping'] == '')) {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -filter_complex [v:0]split=outputs=1[map:v:0] -map [map:v:0]';
			}
			else {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_mapping'] ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_mapping'] : '');
			}

			$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -c:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vcodec'];

			if ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vcodec'] != 'copy') {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -preset:v ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_preset'];
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -profile:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vprofile'];
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -level:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vlevel'];
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -g ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_keyframe_interval'] != '' ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_keyframe_interval'] : '250');
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= (!$adaptive_profile ? ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_resolution'] != '' ? ' -s ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_resolution'] : '') : '');
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_crf'] != '' ? ' -crf ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_crf'] : '') . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vframerate'] != 0 ? ' -r ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vframerate'] : '');
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -b:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_avbitrate'] . 'k ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_minvbitrate'] ? ' -minrate:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_minvbitrate'] . 'k' : '') . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_maxvbitrate'] ? ' -maxrate:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_maxvbitrate'] . 'k' : '');
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -bufsize ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_buffsize'] != 0 ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_buffsize'] : $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_avbitrate']) . 'k';
			}

			$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' ' . $obf_DQEyPz02Ej8BJA07BTkaMQkdMzQ3LTI . ' -c:a ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_acodec'] != 'none' ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_acodec'] : 'copy') . ' ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_acodec'] != 'copy' ? ' -b:a ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_abitrate'] ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_abitrate'] : '128') . 'k -ar 48000 -ac 2 -strict -2' : '');
			break;
		case 'quicksync':
			if (($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo'] == '') && !$adaptive_profile && ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_mapping'] == '')) {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -map v:0';
			}
			else {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_mapping'] ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_mapping'] : '');
			}

			if ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcooding_hwacceleration'] == 'full') {
				if ($obf_DRksLSs2XC0UXBMpPBoDCTQiLisjPzI && $obf_DTAEGSUKGhwKOAYlGhgwGxg1FyYDCSI) {
					$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -vf "fps=fps=' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vframerate'] . ',scale_qsv=w=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[0] . ':h=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[1] . '"';
				}
				else if ($obf_DRksLSs2XC0UXBMpPBoDCTQiLisjPzI && !$obf_DTAEGSUKGhwKOAYlGhgwGxg1FyYDCSI) {
					$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -vf "fps=fps=' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vframerate'] . '"';
				}
				else if (!$obf_DRksLSs2XC0UXBMpPBoDCTQiLisjPzI && $obf_DTAEGSUKGhwKOAYlGhgwGxg1FyYDCSI) {
					$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -vf "scale_qsv=w=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[0] . ':h=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[1] . '"';
				}
				else if (!$obf_DRksLSs2XC0UXBMpPBoDCTQiLisjPzI && !$obf_DTAEGSUKGhwKOAYlGhgwGxg1FyYDCSI) {
					$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= '';
				}
			}
			else if ($obf_DRksLSs2XC0UXBMpPBoDCTQiLisjPzI && $obf_DTAEGSUKGhwKOAYlGhgwGxg1FyYDCSI) {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -vf "fps=fps=' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vframerate'] . ',scale=w=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[0] . ':h=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[1] . '"';
			}
			else if ($obf_DRksLSs2XC0UXBMpPBoDCTQiLisjPzI && !$obf_DTAEGSUKGhwKOAYlGhgwGxg1FyYDCSI) {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -vf "fps=fps=' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vframerate'] . '"';
			}
			else if (!$obf_DRksLSs2XC0UXBMpPBoDCTQiLisjPzI && $obf_DTAEGSUKGhwKOAYlGhgwGxg1FyYDCSI) {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -vf "scale=w=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[0] . ':h=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[1] . '"';
			}
			else if (!$obf_DRksLSs2XC0UXBMpPBoDCTQiLisjPzI && !$obf_DTAEGSUKGhwKOAYlGhgwGxg1FyYDCSI) {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= '';
			}

			$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -c:v ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vcodec'];

			if ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vcodec'] != 'copy') {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -preset:v ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_preset'];
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -profile:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vprofile'];
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -level:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vlevel'];
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -g ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_keyframe_interval'] != '' ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_keyframe_interval'] : '250');
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -b:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_avbitrate'] . 'k ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_minvbitrate'] ? ' -minrate:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_minvbitrate'] . 'k' : '') . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_maxvbitrate'] ? ' -maxrate:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_maxvbitrate'] . 'k' : '');
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -bufsize ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_buffsize'] != 0 ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_buffsize'] : $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_avbitrate']) . 'k';
			}

			$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' ' . $obf_DQEyPz02Ej8BJA07BTkaMQkdMzQ3LTI . ' -c:a ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_acodec'] != 'none' ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_acodec'] : 'copy') . ' ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_acodec'] != 'copy' ? ' -b:a ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_abitrate'] ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_abitrate'] : '128') . 'k -ar 48000 -ac 2 -strict -2' : '');
			break;
		case 'vaapi':
			if (($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo'] == '') && !$adaptive_profile && ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_mapping'] == '')) {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -map v:0';
			}
			else {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_mapping'] ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_mapping'] : '');
			}
			if ($obf_DRksLSs2XC0UXBMpPBoDCTQiLisjPzI && $obf_DTAEGSUKGhwKOAYlGhgwGxg1FyYDCSI) {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -vf "fps=fps=' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vframerate'] . ',scale_vaapi=w=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[0] . ':h=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[1] . '"';
			}
			else if ($obf_DRksLSs2XC0UXBMpPBoDCTQiLisjPzI && !$obf_DTAEGSUKGhwKOAYlGhgwGxg1FyYDCSI) {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -vf "fps=fps=' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vframerate'] . '"';
			}
			else if (!$obf_DRksLSs2XC0UXBMpPBoDCTQiLisjPzI && $obf_DTAEGSUKGhwKOAYlGhgwGxg1FyYDCSI) {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -vf "scale_vaapi=w=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[0] . ':h=' . $obf_DQUEPxYfCjxACjAZL1wqOyELAUnNhE[1] . '"';
			}
			else if (!$obf_DRksLSs2XC0UXBMpPBoDCTQiLisjPzI && !$obf_DTAEGSUKGhwKOAYlGhgwGxg1FyYDCSI) {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= '';
			}

			$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -c:v ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vcodec'];

			if ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vcodec'] != 'copy') {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -preset:v ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_preset'];
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -profile:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vprofile'];
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -level:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vlevel'];
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -g ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_keyframe_interval'] != '' ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_keyframe_interval'] : '250');
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -b:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_avbitrate'] . 'k ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_minvbitrate'] ? ' -minrate:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_minvbitrate'] . 'k' : '') . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_maxvbitrate'] ? ' -maxrate:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_maxvbitrate'] . 'k' : '');
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -bufsize ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_buffsize'] != 0 ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_buffsize'] : $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_avbitrate']) . 'k';
			}

			$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' ' . $obf_DQEyPz02Ej8BJA07BTkaMQkdMzQ3LTI . ' -c:a ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_acodec'] != 'none' ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_acodec'] : 'copy') . ' ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_acodec'] != 'copy' ? ' -b:a ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_abitrate'] ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_abitrate'] : '128') . 'k -ar 48000 -ac 2 -strict -2' : '');
			break;
		case 'gpu':
			if (($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_logo'] == '') && !$adaptive_profile && ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_mapping'] == '')) {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -filter_complex [v:0]split=outputs=1[map:v:0] -map [map:v:0]';
			}
			else {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_mapping'] ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_mapping'] : '');
			}

			if ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vcodec'] != 'copy') {
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -c:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vcodec'];
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -preset:v ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_preset'];
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -profile:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vprofile'];
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -level:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vlevel'];
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -g ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_keyframe_interval'] != '' ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_keyframe_interval'] : '250');
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -flags:v +global_header+cgop';
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -b:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_avbitrate'] . 'k ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_minvbitrate'] ? ' -minrate:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_minvbitrate'] . 'k' : '') . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_maxvbitrate'] ? ' -maxrate:v:0 ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_maxvbitrate'] . 'k' : '');
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vframerate'] != 0 ? ' -r ' . $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_vframerate'] : '');
				$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' -bufsize ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_buffsize'] != 0 ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_buffsize'] : $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_avbitrate']) . 'k';
			}

			$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= ' ' . $obf_DQEyPz02Ej8BJA07BTkaMQkdMzQ3LTI . ' -c:a ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_acodec'] != 'none' ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_acodec'] : 'copy') . ' ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_acodec'] != 'copy' ? ' -b:a ' . ($obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_abitrate'] ? $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_abitrate'] : '128') . 'k -ar 48000 -ac 2 -strict -2' : '');
			break;
		}
	}
	else {
		$obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE .= $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_own_command'];
	}

	return $obf_DQoxPBUDSgmDysKVwmAhAfMRAFOxE;
}

function set_transcoding_method($transcode_id)
{
	global $db;
	$obf_DS4JGRgFLQ0THBECOyUNH1wmEzMVOAE = [$transcode_id];
	$obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE = $db->query('SELECT transcoding_method FROM cms_transcoding WHERE transcoding_id = ?', $obf_DS4JGRgFLQ0THBECOyUNH1wmEzMVOAE);
	return $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_method'];
}

function set_transcoding_resolution($transcode_id)
{
	global $db;
	$obf_DS4JGRgFLQ0THBECOyUNH1wmEzMVOAE = [$transcode_id];
	$obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE = $db->query('SELECT transcoding_resolution FROM cms_transcoding WHERE transcoding_id = ?', $obf_DS4JGRgFLQ0THBECOyUNH1wmEzMVOAE);
	return $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_resolution'];
}

function set_transcoding_deinterlace($transcode_id)
{
	global $db;
	$obf_DS4JGRgFLQ0THBECOyUNH1wmEzMVOAE = [$transcode_id];
	$obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE = $db->query('SELECT transcoding_deinterlace FROM cms_transcoding WHERE transcoding_id = ?', $obf_DS4JGRgFLQ0THBECOyUNH1wmEzMVOAE);
	return $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_deinterlace'];
}

function set_stream_transcoding_cuvid($transcode_id)
{
	global $db;
	$obf_DS4JGRgFLQ0THBECOyUNH1wmEzMVOAE = [$transcode_id];
	$obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE = $db->query('SELECT transcoding_cuvid FROM cms_transcoding WHERE transcoding_id = ?', $obf_DS4JGRgFLQ0THBECOyUNH1wmEzMVOAE);
	return $obf_DT5bDy0JLgUMOQ89HgY7ISUQLUAqBxE[0]['transcoding_cuvid'];
}

function ffmpeg_live_command($stream_id, $time = 10, $stream_loop = 0, $stream_db)
{
	global $db;
	$obf_DVwEPhIkLh4QDwUvBAIfLxwhFywwJhE = false;
	$obf_DTkMxEdEycLATkLEAwKggwIzU2AxE = '';
	$i = 0;
	$stream_id = $stream_db['stream_id'];
	delete_sys_if_exists($stream_id);

	if ($stream_db['stream_method'] == 5) {
		$obf_DRMVHwgaATIuPhATAxEMATspISIHKjI = json_decode($stream_db['stream_adaptive_profile'], true);

		foreach ($obf_DRMVHwgaATIuPhATAxEMATspISIHKjI as $key => $profile) {
			shell_exec('ps aux | grep \'/home/xapicode/iptv_xapicode/streams/' . $stream_id . '' . $key . '_.m3u8\' | grep -v grep | awk \'{print $2}\' | xargs kill -9  > /dev/null 2>/dev/null &');
		}
	}
	else {
		shell_exec('ps aux | grep \'/home/xapicode/iptv_xapicode/streams/' . $stream_id . '_.m3u8\' | grep -v grep | awk \'{print $2}\' | xargs kill -9 > /dev/null 2>/dev/null &');
	}

	$obf_DS01JCQqPD8eKlwjXEAqPDYjIwwJDDI = json_decode($stream_db['stream_status'], true);
	if (($obf_DS01JCQqPD8eKlwjXEAqPDYjIwwJDDI[0][SERVER] == 3) || ($obf_DS01JCQqPD8eKlwjXEAqPDYjIwwJDDI[0][SERVER] == 4)) {
		$obf_DS01JCQqPD8eKlwjXEAqPDYjIwwJDDI[0][SERVER] = 6;
		delete_stream_data($stream_id);
	}
	else if ($obf_DS01JCQqPD8eKlwjXEAqPDYjIwwJDDI[0][SERVER] == 0) {
		$obf_DS01JCQqPD8eKlwjXEAqPDYjIwwJDDI[0][SERVER] = 7;
		delete_stream_data($stream_id);
	}

	if ($stream_loop != 1) {
		$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_status' => json_encode($obf_DS01JCQqPD8eKlwjXEAqPDYjIwwJDDI), 'stream_id' => $stream_id];
		$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_status = :stream_status WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
	}
	else {
		$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_loop_from_status' => 6, 'stream_id' => $stream_id];
		$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_loop_from_status = :stream_loop_from_status WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
	}

	if ($stream_db['stream_hashcode_id'] != NULL) {
		$obf_DTwqPCIfFjMMMA0QNBcSFkAVCQIrMCI = [$stream_db['stream_hashcode_id']];
		$obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE = $db->query('SELECT * FROM cms_hashcode WHERE hashcode_id = ?', $obf_DTwqPCIfFjMMMA0QNBcSFkAVCQIrMCI);
		$obf_DVwEPhIkLh4QDwUvBAIfLxwhFywwJhE = true;
	}

	$obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE = json_decode($stream_db['stream_play_pool'], true);
	$obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE = $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE[$stream_db['stream_play_pool_id']];
	$obf_DSItLz0OPCgPOT0MMgQZBhgbIic5HRE = $db->query('SELECT setting_stream_analyze, setting_stream_probesize FROM cms_settings');
	$obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE = $obf_DSItLz0OPCgPOT0MMgQZBhgbIic5HRE[0]['setting_stream_probesize'];
	$obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE = $obf_DSItLz0OPCgPOT0MMgQZBhgbIic5HRE[0]['setting_stream_analyze'];

	if (!$obf_DVwEPhIkLh4QDwUvBAIfLxwhFywwJhE) {
		if ($stream_db['stream_transcode_id'] == 0) {
			if ($stream_db['stream_method'] == 5) {
				if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
					$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
					$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 5s ' . DOCROOT . 'bin/ffprobe ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -i "' . $obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI . '" -v quiet -print_format json -select_streams v:0 -show_entries stream=codec_name -show_streams 2>&1';
				}
				else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
					$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 5s ' . DOCROOT . 'bin/ffprobe ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -i "' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '" -v quiet -print_format json -select_streams v:0 -show_entries stream=codec_name -show_streams 2>&1';
				}
				else {
					$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 5s ' . DOCROOT . 'bin/ffprobe ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -i "' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '" -v quiet -print_format json -select_streams v:0 -show_entries stream=codec_name -show_streams 2>&1';
				}

				$obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE = json_decode(shell_exec($obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE), true);
				$obf_DRMVHwgaATIuPhATAxEMATspISIHKjI = json_decode($stream_db['stream_adaptive_profile'], true);
				$o = 1;
				$j = 1;
				$inout = [];
				$in = [];

				foreach ($obf_DRMVHwgaATIuPhATAxEMATspISIHKjI as $key => $profile) {
					$obf_DR4bBSw1WzUzQAknGBwKPC0NFDYiByI = set_transcoding_method($profile['profile_id']);
					$resolution = set_transcoding_resolution($profile['profile_id']);
					$obf_DRIuFlwFGhFAMTEONRgxHlwIQBoEJiI = explode('x', $resolution);

					if ($obf_DR4bBSw1WzUzQAknGBwKPC0NFDYiByI == 'gpu') {
						$inout[] = '[in' . $j . ']scale_npp=w=' . $obf_DRIuFlwFGhFAMTEONRgxHlwIQBoEJiI[0] . ':h=' . $obf_DRIuFlwFGhFAMTEONRgxHlwIQBoEJiI[1] . '[out' . $j . ']';
					}
					else {
						$inout[] = '[in' . $j . ']scale_npp=' . $resolution . '[out' . $j . ']';
					}

					$in[] = '[in' . $j . ']';
					$j++;
				}

				switch ($obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['programs'][0]['streams'][0]['codec_name']) {
				case 'mpeg2video':
					$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'mpeg2_cuvid';
					break;
				case 'h264':
					$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'h264_cuvid';
					break;
				case 'hevc':
					$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'hevc_cuvid';
					break;
				default:
					$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'h264_cuvid';
				}

				foreach ($obf_DRMVHwgaATIuPhATAxEMATspISIHKjI as $key => $profile) {
					$obf_DUAFDjgNCR0oASwnIjQHPQkpQAYYKDI = [$profile['profile_id']];
					$obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE = $db->query('SELECT * FROM cms_transcoding WHERE transcoding_id = ?', $obf_DUAFDjgNCR0oASwnIjQHPQkpQAYYKDI);
					$obf_DR4bBSw1WzUzQAknGBwKPC0NFDYiByI = set_transcoding_method($profile['profile_id']);

					if ($obf_DR4bBSw1WzUzQAknGBwKPC0NFDYiByI == 'gpu') {
						$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = set_transcoding_profile($profile['profile_id'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['width'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['height'], true);
						if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
							$obf_DRsdDAYNDQcuHDQWPxU5PD4OHQQCHRE[] = ['ffmpeg' => DOCROOT . 'bin/nvenc/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -hwaccel cuvid -hwaccel_device ' . $profile['gpu_id'] . ' -c:v ' . $obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE . ' -recv_buffer_size 67108864 -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" -filter_complex \'[0:v]split=' . count($obf_DRMVHwgaATIuPhATAxEMATspISIHKjI) . '' . implode('', $in) . ';' . implode(';', $inout) . '\'', 'transcoding' => ' -map \'[out' . $o . ']\' ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -map 0:a:0 -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '' . $key . '_.m3u8'];
						}
						else {
							$obf_DRsdDAYNDQcuHDQWPxU5PD4OHQQCHRE[] = ['ffmpeg' => DOCROOT . 'bin/nvenc/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 ' . ($stream_db['stream_format_flags'] != '' ? ' -correct_ts_overflow 0 -avoid_negative_ts disabled -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -hwaccel cuvid -hwaccel_device ' . $profile['gpu_id'] . ' -c:v ' . $obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" -filter_complex \'[0:v]split=' . count($obf_DRMVHwgaATIuPhATAxEMATspISIHKjI) . '' . implode('', $in) . ';' . implode(';', $inout) . '\'', 'transcoding' => ' -map \'[out' . $o . ']\' ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -map 0:a:0 -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '' . $key . '_.m3u8'];
						}
					}
					else if ($obf_DR4bBSw1WzUzQAknGBwKPC0NFDYiByI == 'cpu') {
						$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = set_transcoding_profile($profile['profile_id'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['width'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['height'], false);
						if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
							$obf_DRsdDAYNDQcuHDQWPxU5PD4OHQQCHRE[] = ['ffmpeg' => DOCROOT . 'bin/ffmpeg -y -loglevel error ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -correct_ts_overflow 0 -avoid_negative_ts disabled -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '"', 'transcoding' => $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -individual_header_trailer 0 -f segment -segment_format mpegts -segment_time 10 -segment_list_size 6 -segment_format_options mpegts_flags=+initial_discontinuity:mpegts_copyts=1 -segment_list_type m3u8 -segment_list_flags +live+delete -segment_list ' . DOCROOT . 'streams/' . $stream_id . '' . $key . '_.m3u8 ' . DOCROOT . 'streams/' . $stream_id . '' . $key . '_%d.ts'];
						}
						else {
							$obf_DRsdDAYNDQcuHDQWPxU5PD4OHQQCHRE[] = ['ffmpeg' => DOCROOT . 'bin/ffmpeg -y -loglevel error ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -correct_ts_overflow 0 -avoid_negative_ts disabled -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '"', 'transcoding' => $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -individual_header_trailer 0 -f segment -segment_format mpegts -segment_time 10 -segment_list_size 6 -segment_format_options mpegts_flags=+initial_discontinuity:mpegts_copyts=1 -segment_list_type m3u8 -segment_list_flags +live+delete -segment_list ' . DOCROOT . 'streams/' . $stream_id . '' . $key . '_.m3u8 ' . DOCROOT . 'streams/' . $stream_id . '' . $key . '_%d.ts'];
						}
					}

					$o++;
				}

				$l = 0;
				$obf_DQcHWxMxCB1bNRkeXDInHzUJNQ07KAE = '';

				foreach ($obf_DRsdDAYNDQcuHDQWPxU5PD4OHQQCHRE as $key => $value) {
					if ($l == 0) {
						$obf_DQcHWxMxCB1bNRkeXDInHzUJNQ07KAE .= $value['ffmpeg'] . $value['transcoding'];
					}
					else {
						$obf_DQcHWxMxCB1bNRkeXDInHzUJNQ07KAE .= $value['transcoding'];
					}

					$l++;
				}

				$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = $obf_DQcHWxMxCB1bNRkeXDInHzUJNQ07KAE . ' -hide_banner -loglevel info';
			}
			else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
				$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
				$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -y -nostdin -hide_banner -loglevel error -err_detect ignore_err -nofix_dts -start_at_zero -copyts -vsync 0 -correct_ts_overflow 0 -avoid_negative_ts disabled -max_interleave_delta 0 ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI) . '" -vcodec copy -scodec copy -acodec copy -individual_header_trailer 0 -f segment -segment_format mpegts -segment_time ' . $time . ' -segment_list_size 6 -segment_format_options mpegts_flags=+initial_discontinuity:mpegts_copyts=1 -segment_list_type m3u8 -segment_list_flags +live+delete -segment_list ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 ' . DOCROOT . 'streams/' . $stream_id . '_%d.ts';
			}
			else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
				$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -y -nostdin -hide_banner -loglevel error -err_detect ignore_err -nofix_dts -start_at_zero -copyts -vsync 0 -correct_ts_overflow 0 -avoid_negative_ts disabled -max_interleave_delta 0 ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" -vcodec copy -scodec copy -acodec copy -individual_header_trailer 0 -f segment -segment_format mpegts -segment_time ' . $time . ' -segment_list_size 6 -segment_format_options mpegts_flags=+initial_discontinuity:mpegts_copyts=1 -segment_list_type m3u8 -segment_list_flags +live+delete -segment_list ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 ' . DOCROOT . 'streams/' . $stream_id . '_%d.ts';
			}
			else {
				$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -y -nostdin -hide_banner -loglevel error -err_detect ignore_err -nofix_dts -start_at_zero -copyts -vsync 0 -correct_ts_overflow 0 -avoid_negative_ts disabled -max_interleave_delta 0 ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" -vcodec copy -scodec copy -acodec copy -individual_header_trailer 0 -f segment -segment_format mpegts -segment_time ' . $time . ' -segment_list_size 6 -segment_format_options mpegts_flags=+initial_discontinuity:mpegts_copyts=1 -segment_list_type m3u8 -segment_list_flags +live+delete -segment_list ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 ' . DOCROOT . 'streams/' . $stream_id . '_%d.ts';
			}
		}
		else {
			$obf_DUAFDjgNCR0oASwnIjQHPQkpQAYYKDI = [$stream_db['stream_transcode_id']];
			$obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE = $db->query('SELECT * FROM cms_transcoding WHERE transcoding_id = ?', $obf_DUAFDjgNCR0oASwnIjQHPQkpQAYYKDI);
			if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
				$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
				$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 10s ' . DOCROOT . 'bin/ffprobe ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -i "' . $obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI . '" -v quiet -print_format json -select_streams v:0 -show_entries stream=codec_name -show_streams 2>&1';
			}
			else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
				$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 10s ' . DOCROOT . 'bin/ffprobe ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -i "' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '" -v quiet -print_format json -select_streams v:0 -show_entries stream=codec_name -show_streams 2>&1';
			}
			else {
				$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 10s ' . DOCROOT . 'bin/ffprobe ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -i "' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '" -v quiet -print_format json -select_streams v:0 -show_entries stream=codec_name -show_streams 2>&1';
			}

			$obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE = json_decode(shell_exec($obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE), true);
			file_put_contents(DOCROOT . 'tmp/' . $stream_id . '_ffprobe.txt', $obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE);
			$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = set_transcoding_profile($stream_db['stream_transcode_id'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['width'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['height']);

			if ($stream_db['transcoding_method'] == 'cpu') {
				if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
					$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -recv_buffer_size 67108864 -i "' . trim($obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
				}
				else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -recv_buffer_size 67108864 -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
				}
				else {
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
				}
			}
			else if ($stream_db['transcoding_method'] == 'gpu') {
				switch ($obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['programs'][0]['streams'][0]['codec_name']) {
				case 'mpeg2video':
					$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'mpeg2_cuvid';
					break;
				case 'h264':
					$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'h264_cuvid';
					break;
				case 'hevc':
					$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'hevc_cuvid';
					break;
				default:
					$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'h264_cuvid';
				}
				if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
					$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/nvenc/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel_device ' . ($stream_db['stream_transcode_gpu_id'] ? $stream_db['stream_transcode_gpu_id'] : 0) . ' -hwaccel cuvid ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -c:v ' . $obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE . ' -surfaces 8 ' . ($stream_db['transcoding_deinterlace'] != 0 ? '-deint adaptive' : '') . ' ' . ($stream_db['transcoding_resolution'] != '' ? '-resize ' . $stream_db['transcoding_resolution'] : '') . ' -drop_second_field 1 -recv_buffer_size 67108864 -i "' . trim($obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
				}
				else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/nvenc/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel_device ' . ($stream_db['stream_transcode_gpu_id'] ? $stream_db['stream_transcode_gpu_id'] : 0) . ' -hwaccel cuvid ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -c:v ' . $obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE . ' -surfaces 8 ' . ($stream_db['transcoding_deinterlace'] != 0 ? '-deint adaptive' : '') . ' ' . ($stream_db['transcoding_resolution'] != '' ? '-resize ' . $stream_db['transcoding_resolution'] : '') . ' -drop_second_field 1 -recv_buffer_size 67108864 -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
				}
				else {
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/nvenc/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -hwaccel_device ' . ($stream_db['stream_transcode_gpu_id'] ? $stream_db['stream_transcode_gpu_id'] : 0) . ' ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel cuvid ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -c:v ' . $obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE . ' -surfaces 8 ' . ($stream_db['transcoding_deinterlace'] != 0 ? '-deint adaptive' : '') . ' ' . ($stream_db['transcoding_resolution'] != '' ? '-resize ' . $stream_db['transcoding_resolution'] : '') . ' -drop_second_field 1 -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
				}
			}
			else if ($stream_db['transcoding_method'] == 'quicksync') {
				if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
					$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_hwacceleration'] != 'encoding_only' ? '-hwaccel qsv -c:v ' . $stream_db['transcoding_cuvid'] . '' : '-init_hw_device qsv=qsv:hw -filter_hw_device qsv') . ' ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -recv_buffer_size 67108864  -i "' . trim($obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' ' . ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_encoder'] == 'hevc_qsv' ? '-load_plugin hevc_hw' : '') . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
				}
				else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_hwacceleration'] != 'encoding_only' ? '-hwaccel qsv -c:v ' . $stream_db['transcoding_cuvid'] . '' : '-init_hw_device qsv=qsv:hw -filter_hw_device qsv') . ' ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -recv_buffer_size 67108864  -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' ' . ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_encoder'] == 'hevc_qsv' ? '-load_plugin hevc_hw' : '') . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
				}
				else {
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_hwacceleration'] != 'encoding_only' ? '-hwaccel qsv -c:v ' . $stream_db['transcoding_cuvid'] . '' : '-init_hw_device qsv=qsv:hw -filter_hw_device qsv') . ' ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -recv_buffer_size 67108864  -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' ' . ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_encoder'] == 'hevc_qsv' ? '-load_plugin hevc_hw' : '') . ' -sn  -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
				}
			}
			else if ($stream_db['transcoding_method'] == 'vaapi') {
				if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
					$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel vaapi -hwaccel_device /dev/dri/renderD128 -hwaccel_output_format vaapi ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -recv_buffer_size 67108864 -i "' . trim($obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
				}
				else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel vaapi -hwaccel_device /dev/dri/renderD128 -hwaccel_output_format vaapi ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -recv_buffer_size 67108864 -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
				}
				else {
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel vaapi -hwaccel_device /dev/dri/renderD128 -hwaccel_output_format vaapi ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -recv_buffer_size 67108864 -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn  -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
				}
			}
			else if ($stream_db['transcoding_method'] == 'own') {
				$obf_DUASCxsTAxwtBiwLEj88Kw01JgQlAjI = explode('|', $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI);

				switch ($obf_DUASCxsTAxwtBiwLEj88Kw01JgQlAjI[0]) {
				case 'cpu':
					$obf_DQoFCzkmCRM4FxA5Hz4RNwI4NhNcESI = DOCROOT . 'bin/ffmpeg';
					break;
				case 'gpu':
					$obf_DQoFCzkmCRM4FxA5Hz4RNwI4NhNcESI = DOCROOT . 'bin/nvenc/streaming/bin/ffmpeg';
					break;
				case 'quicksync':
					$obf_DQoFCzkmCRM4FxA5Hz4RNwI4NhNcESI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg';
					break;
				case 'vaapi':
					$obf_DQoFCzkmCRM4FxA5Hz4RNwI4NhNcESI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg';
					break;
				}
				if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
					$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
					$search_array = ['-i {INPUT}', '{gpu}'];
					$replace_array = ['-i "' . $obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI . '"', $stream_db['stream_transcode_gpu_id']];
					$obf_DSEzESMMHwoEKgUqKAgRCwMrKTQICgE = str_replace($search_array, $replace_array, $obf_DUASCxsTAxwtBiwLEj88Kw01JgQlAjI[1]);
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = $obf_DQoFCzkmCRM4FxA5Hz4RNwI4NhNcESI . ' ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -correct_ts_overflow 0 -avoid_negative_ts disabled -max_interleave_delta 0 ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' ' . $obf_DSEzESMMHwoEKgUqKAgRCwMrKTQICgE . ' -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8';
				}
				else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
					$search_array = ['-i {INPUT}', '{gpu}'];
					$replace_array = ['-i "' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '"', $stream_db['stream_transcode_gpu_id']];
					$obf_DSEzESMMHwoEKgUqKAgRCwMrKTQICgE = str_replace($search_array, $replace_array, $obf_DUASCxsTAxwtBiwLEj88Kw01JgQlAjI[1]);
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = $obf_DQoFCzkmCRM4FxA5Hz4RNwI4NhNcESI . ' ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -correct_ts_overflow 0 -avoid_negative_ts disabled -max_interleave_delta 0 ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . $obf_DSEzESMMHwoEKgUqKAgRCwMrKTQICgE . ' -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8';
				}
				else {
					$search_array = ['-i {INPUT}', '{gpu}'];
					$replace_array = ['-i "' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '"', $stream_db['stream_transcode_gpu_id']];
					$obf_DSEzESMMHwoEKgUqKAgRCwMrKTQICgE = str_replace($search_array, $replace_array, $obf_DUASCxsTAxwtBiwLEj88Kw01JgQlAjI[1]);
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = $obf_DQoFCzkmCRM4FxA5Hz4RNwI4NhNcESI . ' ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -correct_ts_overflow 0 -avoid_negative_ts disabled -max_interleave_delta 0 ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' ' . $obf_DSEzESMMHwoEKgUqKAgRCwMrKTQICgE . ' -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8';
				}
			}
		}
	}
	else {
		$obf_DRIQCRxcMhoLMwkaJh8JDTs1FCIhNyI = $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_image'];
		$obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI = $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_vbitrate'];
		$obf_DTY3MzU5PBUhPlwHMi0uITMzGy8zJhE = explode('-', $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_scale']);
		$obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI = explode('-', $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_padding']);
		$obf_DQEGEyEOLhAfKDgYNDYLAz8XGx4yKyI = $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI + $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI;

		if ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_method'] == 1) {
			$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/dehash/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel qsv -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -c:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_cuvid'] . ' -recv_buffer_size 67108864 -i ' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . ' -filter_complex \'[v:0]hwdownload,format=pix_fmts=nv12[format:0]; [format:0]cvdelogo=filename=' . DOCROOT . 'image/' . $obf_DRIQCRxcMhoLMwkaJh8JDTs1FCIhNyI . ':buffer_queue_size=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_buffer_queue_size'] . ':detect_interval=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_dedect_interval'] . ':score_min=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_score_min'] . ':scale_min=' . $obf_DTY3MzU5PBUhPlwHMi0uITMzGy8zJhE[0] . ':scale_max=' . $obf_DTY3MzU5PBUhPlwHMi0uITMzGy8zJhE[1] . ':padding_left=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[0] . ':padding_right=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[1] . ':padding_top=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[2] . ':padding_bottom=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[3] . '[cvdelogo]; [cvdelogo]split=outputs=1[hwupload:0]; [hwupload:0]hwupload=extra_hw_frames=10[map:v:0]\' -map [map:v:0] -c:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_encoder'] . ' ' . ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_encoder'] == 'hevc_qsv' ? '-load_plugin hevc_hw' : '') . ' -flags:v +global_header+cgop -preset:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_preset'] . ' -profile:v:0 main -g 60 ' . ($obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI != 'auto' ? '-b:v:0 ' . $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI . 'k -maxrate:v:0 ' . $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI . 'k -bufsize:v:0 ' . $obf_DQEGEyEOLhAfKDgYNDYLAz8XGx4yKyI . 'k' : '') . ' -map a:0 ' . ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_acodec'] != 'copy' ? '-c:a ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_acodec'] . ' -ar 44100 -b:a:0 128k' : '-c:a copy') . ' -max_muxing_queue_size 512 -f tee "[select=\\\'v:0,a:0\\\':bsfs/v=dump_extra=freq=keyframe:f=hls:hls_time=10:hls_list_size=6:hls_flags=delete_segments:var_stream_map=\\\'v:0,a:0\\\':hls_segment_filename=' . DOCROOT . 'streams/' . $stream_id . '_%d.ts]"' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -nostdin -hide_banner -loglevel info';
		}
		else if ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_method'] == 2) {
			$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/nvenc/dehash/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel_device ' . ($stream_db['stream_hashcode_gpu_id'] ? $stream_db['stream_hashcode_gpu_id'] : 0) . ' -hwaccel cuvid -c:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_cuvid'] . ' -recv_buffer_size 67108864 ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i ' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . ' -filter_complex "[v:0]hwdownload,format=pix_fmts=nv12[format:0]; [format:0]cvdelogo=filename=' . DOCROOT . 'image/' . $obf_DRIQCRxcMhoLMwkaJh8JDTs1FCIhNyI . ':buffer_queue_size=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_buffer_queue_size'] . ':detect_interval=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_dedect_interval'] . ':score_min=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_score_min'] . ':scale_min=' . $obf_DTY3MzU5PBUhPlwHMi0uITMzGy8zJhE[0] . ':scale_max=' . $obf_DTY3MzU5PBUhPlwHMi0uITMzGy8zJhE[1] . ':padding_left=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[0] . ':padding_right=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[1] . ':padding_top=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[2] . ':padding_bottom=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[3] . '[cvdelogo]; [cvdelogo]split=outputs=1[hwupload:0]; [hwupload:0]hwupload=extra_hw_frames=1[map:v:0]" -map [map:v:0] -c:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_encoder'] . ' -flags:v +global_header+cgop -preset:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_preset'] . ' -profile:v:0 high -g 60 ' . ($obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI != 'auto' ? '-b:v:0 ' . $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI . 'k -maxrate:v:0 ' . $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI . 'k -bufsize:v:0 ' . $obf_DQEGEyEOLhAfKDgYNDYLAz8XGx4yKyI . 'k' : '') . ' -map a:0 ' . ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_acodec'] != 'copy' ? '-c:a ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_acodec'] . ' -ar 44100 -b:a:0 128k' : '-c:a copy') . ' -max_muxing_queue_size 512 -f tee "[select=\\\'v:0,a:0\\\':bsfs/v=dump_extra=freq=keyframe:f=hls:hls_time=10:hls_list_size=6:hls_flags=delete_segments:var_stream_map=\\\'v:0,a:0\\\':hls_segment_filename=' . DOCROOT . 'streams/' . $stream_id . '_%d.ts]"' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -nostdin -hide_banner -loglevel info';
		}
		else if ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_method'] == 3) {
			$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/dehash/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -recv_buffer_size 67108864 -i ' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . ' -filter_complex "[v:0]cvdelogo=filename=' . DOCROOT . 'image/' . $obf_DRIQCRxcMhoLMwkaJh8JDTs1FCIhNyI . ':buffer_queue_size=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_buffer_queue_size'] . ':detect_interval=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_dedect_interval'] . ':score_min=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_score_min'] . ':scale_min=' . $obf_DTY3MzU5PBUhPlwHMi0uITMzGy8zJhE[0] . ':scale_max=' . $obf_DTY3MzU5PBUhPlwHMi0uITMzGy8zJhE[1] . ':padding_left=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[0] . ':padding_right=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[1] . ':padding_top=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[2] . ':padding_bottom=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[3] . '[cvdelogo]; [cvdelogo]split=outputs=1[map:v:0]" -map [map:v:0] -c:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_encoder'] . ' -flags:v +global_header+cgop -preset:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_preset'] . ' -g 60 ' . ($obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI != 'auto' ? '-b:v:0 ' . $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI . 'k -maxrate:v:0 ' . $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI . 'k -bufsize:v:0 ' . $obf_DQEGEyEOLhAfKDgYNDYLAz8XGx4yKyI . 'k' : '') . ' -map a:0 ' . ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_acodec'] != 'copy' ? '-c:a ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_acodec'] . ' -ar 44100 -b:a:0 128k' : '-c:a copy') . ' -max_muxing_queue_size 512 -f tee "[select=\\\'v:0,a:0\\\':bsfs/v=dump_extra=freq=keyframe:f=hls:hls_time=10:hls_list_size=6:hls_flags=delete_segments:var_stream_map=\\\'v:0,a:0\\\':hls_segment_filename=' . DOCROOT . 'streams/' . $stream_id . '_%d.ts]"' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -nostdin -hide_banner -loglevel info';
		}
	}

	file_put_contents(DOCROOT . 'tmp/' . $stream_id . '_ffmpeg.txt', $obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI);
	$obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI = shell_exec($obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI . ' >> /home/xapicode/iptv_xapicode/streams/' . $stream_id . '_out.log 2>>/home/xapicode/iptv_xapicode/streams/' . $stream_id . '_error.log & echo $!');

	if (!empty($obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI)) {
		file_put_contents(DOCROOT . 'streams/' . $stream_id . '_checker', $obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI);
		return true;
	}
}

function ffmpeg_ondemand_command($stream_id, $time = 10, $stream_db)
{
	global $db;
	$obf_DVwEPhIkLh4QDwUvBAIfLxwhFywwJhE = false;
	$obf_DTkMxEdEycLATkLEAwKggwIzU2AxE = '';
	$i = 0;
	$stream_id = $stream_db['stream_id'];
	delete_sys_if_exists($stream_id);

	if ($stream_db['stream_method'] == 5) {
		$obf_DRMVHwgaATIuPhATAxEMATspISIHKjI = json_decode($stream_db['stream_adaptive_profile'], true);

		foreach ($obf_DRMVHwgaATIuPhATAxEMATspISIHKjI as $key => $profile) {
			shell_exec('ps aux | grep \'/home/xapicode/iptv_xapicode/streams/' . $stream_id . '' . $key . '_.m3u8\' | grep -v grep | awk \'{print $2}\' | xargs kill -9  > /dev/null 2>/dev/null &');
		}
	}
	else {
		shell_exec('ps aux | grep \'/home/xapicode/iptv_xapicode/streams/' . $stream_id . '_.m3u8\' | grep -v grep | awk \'{print $2}\' | xargs kill -9 > /dev/null 2>/dev/null &');
	}

	if ($stream_db['stream_hashcode_id'] != NULL) {
		$obf_DTwqPCIfFjMMMA0QNBcSFkAVCQIrMCI = [$stream_db['stream_hashcode_id']];
		$obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE = $db->query('SELECT * FROM cms_hashcode WHERE hashcode_id = ?', $obf_DTwqPCIfFjMMMA0QNBcSFkAVCQIrMCI);
		$obf_DVwEPhIkLh4QDwUvBAIfLxwhFywwJhE = true;
	}

	$obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE = json_decode($stream_db['stream_play_pool'], true);
	$obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE = $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE[$stream_db['stream_play_pool_id']];
	$obf_DSItLz0OPCgPOT0MMgQZBhgbIic5HRE = $db->query('SELECT setting_stream_analyze, setting_stream_probesize FROM cms_settings');
	$obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE = $obf_DSItLz0OPCgPOT0MMgQZBhgbIic5HRE[0]['setting_stream_probesize'];
	$obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE = $obf_DSItLz0OPCgPOT0MMgQZBhgbIic5HRE[0]['setting_stream_analyze'];
	if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
		$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
		$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 3s ' . DOCROOT . 'bin/ffprobe ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -i "' . $obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI . '" -v quiet -print_format json -select_streams v:0 -show_entries stream=codec_name -show_streams 2>&1';
	}
	else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
		$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 3s ' . DOCROOT . 'bin/ffprobe ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -i "' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '" -v quiet -print_format json -select_streams v:0 -show_entries stream=codec_name -show_streams 2>&1';
	}
	else {
		$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 3s ' . DOCROOT . 'bin/ffprobe ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -i "' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '" -v quiet -print_format json -select_streams v:0 -show_entries stream=codec_name -show_streams 2>&1';
	}

	$obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE = json_decode(shell_exec($obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE), true);

	if (!array_key_exists('streams', $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE)) {
		return false;
	}
	else {
		if (!$obf_DVwEPhIkLh4QDwUvBAIfLxwhFywwJhE) {
			if ($stream_db['stream_transcode_id'] == 0) {
				if ($stream_db['stream_method'] == 5) {
					$obf_DRMVHwgaATIuPhATAxEMATspISIHKjI = json_decode($stream_db['stream_adaptive_profile'], true);
					$o = 1;
					$j = 1;
					$inout = [];
					$in = [];

					foreach ($obf_DRMVHwgaATIuPhATAxEMATspISIHKjI as $key => $profile) {
						$obf_DR4bBSw1WzUzQAknGBwKPC0NFDYiByI = set_transcoding_method($profile['profile_id']);
						$resolution = set_transcoding_resolution($profile['profile_id']);
						$obf_DRIuFlwFGhFAMTEONRgxHlwIQBoEJiI = explode('x', $resolution);

						if ($obf_DR4bBSw1WzUzQAknGBwKPC0NFDYiByI == 'gpu') {
							$inout[] = '[in' . $j . ']scale_npp=w=' . $obf_DRIuFlwFGhFAMTEONRgxHlwIQBoEJiI[0] . ':h=' . $obf_DRIuFlwFGhFAMTEONRgxHlwIQBoEJiI[1] . '[out' . $j . ']';
						}
						else {
							$inout[] = '[in' . $j . ']scale_npp=' . $resolution . '[out' . $j . ']';
						}

						$in[] = '[in' . $j . ']';
						$j++;
					}

					switch ($obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['programs'][0]['streams'][0]['codec_name']) {
					case 'mpeg2video':
						$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'mpeg2_cuvid';
						break;
					case 'h264':
						$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'h264_cuvid';
						break;
					case 'hevc':
						$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'hevc_cuvid';
						break;
					default:
						$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'h264_cuvid';
					}

					foreach ($obf_DRMVHwgaATIuPhATAxEMATspISIHKjI as $key => $profile) {
						$obf_DUAFDjgNCR0oASwnIjQHPQkpQAYYKDI = [$profile['profile_id']];
						$obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE = $db->query('SELECT * FROM cms_transcoding WHERE transcoding_id = ?', $obf_DUAFDjgNCR0oASwnIjQHPQkpQAYYKDI);
						$obf_DR4bBSw1WzUzQAknGBwKPC0NFDYiByI = set_transcoding_method($profile['profile_id']);

						if ($obf_DR4bBSw1WzUzQAknGBwKPC0NFDYiByI == 'gpu') {
							$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = set_transcoding_profile($profile['profile_id'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['width'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['height'], true);
							if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
								$obf_DRsdDAYNDQcuHDQWPxU5PD4OHQQCHRE[] = ['ffmpeg' => DOCROOT . 'bin/nvenc/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -hwaccel cuvid -hwaccel_device ' . $profile['gpu_id'] . ' -c:v ' . $obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE . ' -recv_buffer_size 67108864 -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" -filter_complex \'[0:v]split=' . count($obf_DRMVHwgaATIuPhATAxEMATspISIHKjI) . '' . implode('', $in) . ';' . implode(';', $inout) . '\'', 'transcoding' => ' -map \'[out' . $o . ']\' ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -map 0:a:0 -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '' . $key . '_.m3u8'];
							}
							else {
								$obf_DRsdDAYNDQcuHDQWPxU5PD4OHQQCHRE[] = ['ffmpeg' => DOCROOT . 'bin/nvenc/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 ' . ($stream_db['stream_format_flags'] != '' ? ' -correct_ts_overflow 0 -avoid_negative_ts disabled -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -hwaccel cuvid -hwaccel_device ' . $profile['gpu_id'] . ' -c:v ' . $obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" -filter_complex \'[0:v]split=' . count($obf_DRMVHwgaATIuPhATAxEMATspISIHKjI) . '' . implode('', $in) . ';' . implode(';', $inout) . '\'', 'transcoding' => ' -map \'[out' . $o . ']\' ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -map 0:a:0 -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '' . $key . '_.m3u8'];
							}
						}
						else if ($obf_DR4bBSw1WzUzQAknGBwKPC0NFDYiByI == 'cpu') {
							$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = set_transcoding_profile($profile['profile_id'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['width'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['height'], false);
							if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
								$obf_DRsdDAYNDQcuHDQWPxU5PD4OHQQCHRE[] = ['ffmpeg' => DOCROOT . 'bin/ffmpeg -y -loglevel error ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -correct_ts_overflow 0 -avoid_negative_ts disabled -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '"', 'transcoding' => $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -individual_header_trailer 0 -f segment -segment_format mpegts -segment_time 10 -segment_list_size 6 -segment_format_options mpegts_flags=+initial_discontinuity:mpegts_copyts=1 -segment_list_type m3u8 -segment_list_flags +live+delete -segment_list ' . DOCROOT . 'streams/' . $stream_id . '' . $key . '_.m3u8 ' . DOCROOT . 'streams/' . $stream_id . '' . $key . '_%d.ts'];
							}
							else {
								$obf_DRsdDAYNDQcuHDQWPxU5PD4OHQQCHRE[] = ['ffmpeg' => DOCROOT . 'bin/ffmpeg -y -loglevel error ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -correct_ts_overflow 0 -avoid_negative_ts disabled -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '"', 'transcoding' => $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -individual_header_trailer 0 -f segment -segment_format mpegts -segment_time 10 -segment_list_size 6 -segment_format_options mpegts_flags=+initial_discontinuity:mpegts_copyts=1 -segment_list_type m3u8 -segment_list_flags +live+delete -segment_list ' . DOCROOT . 'streams/' . $stream_id . '' . $key . '_.m3u8 ' . DOCROOT . 'streams/' . $stream_id . '' . $key . '_%d.ts'];
							}
						}

						$o++;
					}

					$l = 0;
					$obf_DQcHWxMxCB1bNRkeXDInHzUJNQ07KAE = '';

					foreach ($obf_DRsdDAYNDQcuHDQWPxU5PD4OHQQCHRE as $key => $value) {
						if ($l == 0) {
							$obf_DQcHWxMxCB1bNRkeXDInHzUJNQ07KAE .= $value['ffmpeg'] . $value['transcoding'];
						}
						else {
							$obf_DQcHWxMxCB1bNRkeXDInHzUJNQ07KAE .= $value['transcoding'];
						}

						$l++;
					}

					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = $obf_DQcHWxMxCB1bNRkeXDInHzUJNQ07KAE . ' -hide_banner -loglevel info';
				}
				else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
					$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -y -nostdin -hide_banner -loglevel error -err_detect ignore_err -nofix_dts -start_at_zero -copyts -vsync 0 -correct_ts_overflow 0 -avoid_negative_ts disabled -max_interleave_delta 0 ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI) . '" -vcodec copy -scodec copy -acodec copy -individual_header_trailer 0 -f segment -segment_format mpegts -segment_time ' . $time . ' -segment_list_size 6 -segment_format_options mpegts_flags=+initial_discontinuity:mpegts_copyts=1 -segment_list_type m3u8 -segment_list_flags +live+delete -segment_list ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 ' . DOCROOT . 'streams/' . $stream_id . '_%d.ts';
				}
				else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -y -nostdin -hide_banner -loglevel error -err_detect ignore_err -nofix_dts -start_at_zero -copyts -vsync 0 -correct_ts_overflow 0 -avoid_negative_ts disabled -max_interleave_delta 0 ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" -vcodec copy -scodec copy -acodec copy -individual_header_trailer 0 -f segment -segment_format mpegts -segment_time ' . $time . ' -segment_list_size 6 -segment_format_options mpegts_flags=+initial_discontinuity:mpegts_copyts=1 -segment_list_type m3u8 -segment_list_flags +live+delete -segment_list ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 ' . DOCROOT . 'streams/' . $stream_id . '_%d.ts';
				}
				else {
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -y -nostdin -hide_banner -loglevel error -err_detect ignore_err -nofix_dts -start_at_zero -copyts -vsync 0 -correct_ts_overflow 0 -avoid_negative_ts disabled -max_interleave_delta 0 ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" -vcodec copy -scodec copy -acodec copy -individual_header_trailer 0 -f segment -segment_format mpegts -segment_time ' . $time . ' -segment_list_size 6 -segment_format_options mpegts_flags=+initial_discontinuity:mpegts_copyts=1 -segment_list_type m3u8 -segment_list_flags +live+delete -segment_list ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 ' . DOCROOT . 'streams/' . $stream_id . '_%d.ts';
				}
			}
			else {
				$obf_DUAFDjgNCR0oASwnIjQHPQkpQAYYKDI = [$stream_db['stream_transcode_id']];
				$obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE = $db->query('SELECT * FROM cms_transcoding WHERE transcoding_id = ?', $obf_DUAFDjgNCR0oASwnIjQHPQkpQAYYKDI);
				if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
					$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
					$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 10s ' . DOCROOT . 'bin/ffprobe ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -i "' . $obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI . '" -v quiet -print_format json -select_streams v:0 -show_entries stream=codec_name -show_streams 2>&1';
				}
				else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
					$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 10s ' . DOCROOT . 'bin/ffprobe ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -i "' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '" -v quiet -print_format json -select_streams v:0 -show_entries stream=codec_name -show_streams 2>&1';
				}
				else {
					$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 10s ' . DOCROOT . 'bin/ffprobe ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -i "' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '" -v quiet -print_format json -select_streams v:0 -show_entries stream=codec_name -show_streams 2>&1';
				}

				$obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE = json_decode(shell_exec($obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE), true);
				file_put_contents(DOCROOT . 'tmp/' . $stream_id . '_ffprobe.txt', $obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE);
				$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = set_transcoding_profile($stream_db['stream_transcode_id'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['width'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['height']);

				if ($stream_db['transcoding_method'] == 'cpu') {
					if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
						$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
						$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -recv_buffer_size 67108864 -i "' . trim($obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
					}
					else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
						$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -recv_buffer_size 67108864 -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
					}
					else {
						$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
					}
				}
				else if ($stream_db['transcoding_method'] == 'gpu') {
					switch ($obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['programs'][0]['streams'][0]['codec_name']) {
					case 'mpeg2video':
						$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'mpeg2_cuvid';
						break;
					case 'h264':
						$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'h264_cuvid';
						break;
					case 'hevc':
						$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'hevc_cuvid';
						break;
					default:
						$obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE = 'h264_cuvid';
					}
					if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
						$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
						$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/nvenc/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel_device ' . ($stream_db['stream_transcode_gpu_id'] ? $stream_db['stream_transcode_gpu_id'] : 0) . ' -hwaccel cuvid ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -c:v ' . $obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE . ' -surfaces 8 ' . ($stream_db['transcoding_deinterlace'] != 0 ? '-deint adaptive' : '') . ' ' . ($stream_db['transcoding_resolution'] != '' ? '-resize ' . $stream_db['transcoding_resolution'] : '') . ' -drop_second_field 1 -recv_buffer_size 67108864 -i "' . trim($obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
					}
					else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
						$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/nvenc/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel_device ' . ($stream_db['stream_transcode_gpu_id'] ? $stream_db['stream_transcode_gpu_id'] : 0) . ' -hwaccel cuvid ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -c:v ' . $obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE . ' -surfaces 8 ' . ($stream_db['transcoding_deinterlace'] != 0 ? '-deint adaptive' : '') . ' ' . ($stream_db['transcoding_resolution'] != '' ? '-resize ' . $stream_db['transcoding_resolution'] : '') . ' -drop_second_field 1 -recv_buffer_size 67108864 -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
					}
					else {
						$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/nvenc/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -hwaccel_device ' . ($stream_db['stream_transcode_gpu_id'] ? $stream_db['stream_transcode_gpu_id'] : 0) . ' ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel cuvid ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize 5000 -analyzeduration 1000 -c:v ' . $obf_DREmHjYbIyECAwoROBIKCjUNNyQTBgE . ' -surfaces 8 ' . ($stream_db['transcoding_deinterlace'] != 0 ? '-deint adaptive' : '') . ' ' . ($stream_db['transcoding_resolution'] != '' ? '-resize ' . $stream_db['transcoding_resolution'] : '') . ' -drop_second_field 1 -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
					}
				}
				else if ($stream_db['transcoding_method'] == 'quicksync') {
					if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
						$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
						$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_hwacceleration'] != 'encoding_only' ? '-hwaccel qsv -c:v ' . $stream_db['transcoding_cuvid'] . '' : '-init_hw_device qsv=qsv:hw -filter_hw_device qsv') . ' ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -recv_buffer_size 67108864  -i "' . trim($obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' ' . ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_encoder'] == 'hevc_qsv' ? '-load_plugin hevc_hw' : '') . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
					}
					else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
						$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_hwacceleration'] != 'encoding_only' ? '-hwaccel qsv -c:v ' . $stream_db['transcoding_cuvid'] . '' : '-init_hw_device qsv=qsv:hw -filter_hw_device qsv') . ' ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -recv_buffer_size 67108864  -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' ' . ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_encoder'] == 'hevc_qsv' ? '-load_plugin hevc_hw' : '') . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
					}
					else {
						$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_hwacceleration'] != 'encoding_only' ? '-hwaccel qsv -c:v ' . $stream_db['transcoding_cuvid'] . '' : '-init_hw_device qsv=qsv:hw -filter_hw_device qsv') . ' ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -recv_buffer_size 67108864  -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' ' . ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_encoder'] == 'hevc_qsv' ? '-load_plugin hevc_hw' : '') . ' -sn  -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
					}
				}
				else if ($stream_db['transcoding_method'] == 'vaapi') {
					if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
						$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
						$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel vaapi -hwaccel_device /dev/dri/renderD128 -hwaccel_output_format vaapi ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -recv_buffer_size 67108864 -i "' . trim($obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
					}
					else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
						$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel vaapi -hwaccel_device /dev/dri/renderD128 -hwaccel_output_format vaapi ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -recv_buffer_size 67108864 -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
					}
					else {
						$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . ($obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync'] == 'auto' ? '' : '-vsync ' . $obf_DSYIXAULzYnQDkUGTQZCj4QHTk4DxE[0]['transcoding_vsync']) . ' -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel vaapi -hwaccel_device /dev/dri/renderD128 -hwaccel_output_format vaapi ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -recv_buffer_size 67108864 -i "' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -sn  -max_muxing_queue_size 512 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
					}
				}
				else if ($stream_db['transcoding_method'] == 'own') {
					$obf_DUASCxsTAxwtBiwLEj88Kw01JgQlAjI = explode('|', $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI);

					switch ($obf_DUASCxsTAxwtBiwLEj88Kw01JgQlAjI[0]) {
					case 'cpu':
						$obf_DQoFCzkmCRM4FxA5Hz4RNwI4NhNcESI = DOCROOT . 'bin/ffmpeg';
						break;
					case 'gpu':
						$obf_DQoFCzkmCRM4FxA5Hz4RNwI4NhNcESI = DOCROOT . 'bin/nvenc/streaming/bin/ffmpeg';
						break;
					case 'quicksync':
						$obf_DQoFCzkmCRM4FxA5Hz4RNwI4NhNcESI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg';
						break;
					case 'vaapi':
						$obf_DQoFCzkmCRM4FxA5Hz4RNwI4NhNcESI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg';
						break;
					}
					if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE, PHP_URL_HOST) == 'youtu.be')) {
						$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE));
						$search_array = ['-i {INPUT}', '{gpu}'];
						$replace_array = ['-i "' . $obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI . '"', $stream_db['stream_transcode_gpu_id']];
						$obf_DSEzESMMHwoEKgUqKAgRCwMrKTQICgE = str_replace($search_array, $replace_array, $obf_DUASCxsTAxwtBiwLEj88Kw01JgQlAjI[1]);
						$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = $obf_DQoFCzkmCRM4FxA5Hz4RNwI4NhNcESI . ' ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -correct_ts_overflow 0 -avoid_negative_ts disabled -max_interleave_delta 0 ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' ' . $obf_DSEzESMMHwoEKgUqKAgRCwMrKTQICgE . ' -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8';
					}
					else if ((parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'rtmp') && (parse_url($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)['scheme'] != 'udp')) {
						$search_array = ['-i {INPUT}', '{gpu}'];
						$replace_array = ['-i "' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '"', $stream_db['stream_transcode_gpu_id']];
						$obf_DSEzESMMHwoEKgUqKAgRCwMrKTQICgE = str_replace($search_array, $replace_array, $obf_DUASCxsTAxwtBiwLEj88Kw01JgQlAjI[1]);
						$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = $obf_DQoFCzkmCRM4FxA5Hz4RNwI4NhNcESI . ' ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -correct_ts_overflow 0 -avoid_negative_ts disabled -max_interleave_delta 0 ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" ' . $obf_DSEzESMMHwoEKgUqKAgRCwMrKTQICgE . ' -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8';
					}
					else {
						$search_array = ['-i {INPUT}', '{gpu}'];
						$replace_array = ['-i "' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '"', $stream_db['stream_transcode_gpu_id']];
						$obf_DSEzESMMHwoEKgUqKAgRCwMrKTQICgE = str_replace($search_array, $replace_array, $obf_DUASCxsTAxwtBiwLEj88Kw01JgQlAjI[1]);
						$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = $obf_DQoFCzkmCRM4FxA5Hz4RNwI4NhNcESI . ' ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' -correct_ts_overflow 0 -avoid_negative_ts disabled -max_interleave_delta 0 ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' ' . $obf_DSEzESMMHwoEKgUqKAgRCwMrKTQICgE . ' -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8';
					}
				}
			}
		}
		else {
			$obf_DRIQCRxcMhoLMwkaJh8JDTs1FCIhNyI = $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_image'];
			$obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI = $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_vbitrate'];
			$obf_DTY3MzU5PBUhPlwHMi0uITMzGy8zJhE = explode('-', $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_scale']);
			$obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI = explode('-', $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_padding']);
			$obf_DQEGEyEOLhAfKDgYNDYLAz8XGx4yKyI = $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI + $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI;

			if ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_method'] == 1) {
				$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/dehash/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel qsv -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -c:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_cuvid'] . ' -recv_buffer_size 67108864 -i ' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . ' -filter_complex \'[v:0]hwdownload,format=pix_fmts=nv12[format:0]; [format:0]cvdelogo=filename=' . DOCROOT . 'image/' . $obf_DRIQCRxcMhoLMwkaJh8JDTs1FCIhNyI . ':buffer_queue_size=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_buffer_queue_size'] . ':detect_interval=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_dedect_interval'] . ':score_min=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_score_min'] . ':scale_min=' . $obf_DTY3MzU5PBUhPlwHMi0uITMzGy8zJhE[0] . ':scale_max=' . $obf_DTY3MzU5PBUhPlwHMi0uITMzGy8zJhE[1] . ':padding_left=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[0] . ':padding_right=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[1] . ':padding_top=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[2] . ':padding_bottom=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[3] . '[cvdelogo]; [cvdelogo]split=outputs=1[hwupload:0]; [hwupload:0]hwupload=extra_hw_frames=10[map:v:0]\' -map [map:v:0] -c:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_encoder'] . ' ' . ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_encoder'] == 'hevc_qsv' ? '-load_plugin hevc_hw' : '') . ' -flags:v +global_header+cgop -preset:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_preset'] . ' -profile:v:0 main -g 60 ' . ($obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI != 'auto' ? '-b:v:0 ' . $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI . 'k -maxrate:v:0 ' . $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI . 'k -bufsize:v:0 ' . $obf_DQEGEyEOLhAfKDgYNDYLAz8XGx4yKyI . 'k' : '') . ' -map a:0 ' . ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_acodec'] != 'copy' ? '-c:a ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_acodec'] . ' -ar 44100 -b:a:0 128k' : '-c:a copy') . ' -max_muxing_queue_size 512 -f tee "[select=\\\'v:0,a:0\\\':bsfs/v=dump_extra=freq=keyframe:f=hls:hls_time=10:hls_list_size=6:hls_flags=delete_segments:var_stream_map=\\\'v:0,a:0\\\':hls_segment_filename=' . DOCROOT . 'streams/' . $stream_id . '_%d.ts]"' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -nostdin -hide_banner -loglevel info';
			}
			else if ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_method'] == 2) {
				$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/nvenc/dehash/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled -hwaccel_device ' . ($stream_db['stream_hashcode_gpu_id'] ? $stream_db['stream_hashcode_gpu_id'] : 0) . ' -hwaccel cuvid -c:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_cuvid'] . ' -recv_buffer_size 67108864 ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i ' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . ' -filter_complex "[v:0]hwdownload,format=pix_fmts=nv12[format:0]; [format:0]cvdelogo=filename=' . DOCROOT . 'image/' . $obf_DRIQCRxcMhoLMwkaJh8JDTs1FCIhNyI . ':buffer_queue_size=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_buffer_queue_size'] . ':detect_interval=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_dedect_interval'] . ':score_min=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_score_min'] . ':scale_min=' . $obf_DTY3MzU5PBUhPlwHMi0uITMzGy8zJhE[0] . ':scale_max=' . $obf_DTY3MzU5PBUhPlwHMi0uITMzGy8zJhE[1] . ':padding_left=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[0] . ':padding_right=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[1] . ':padding_top=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[2] . ':padding_bottom=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[3] . '[cvdelogo]; [cvdelogo]split=outputs=1[hwupload:0]; [hwupload:0]hwupload=extra_hw_frames=1[map:v:0]" -map [map:v:0] -c:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_encoder'] . ' -flags:v +global_header+cgop -preset:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_preset'] . ' -profile:v:0 high -g 60 ' . ($obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI != 'auto' ? '-b:v:0 ' . $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI . 'k -maxrate:v:0 ' . $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI . 'k -bufsize:v:0 ' . $obf_DQEGEyEOLhAfKDgYNDYLAz8XGx4yKyI . 'k' : '') . ' -map a:0 ' . ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_acodec'] != 'copy' ? '-c:a ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_acodec'] . ' -ar 44100 -b:a:0 128k' : '-c:a copy') . ' -max_muxing_queue_size 512 -f tee "[select=\\\'v:0,a:0\\\':bsfs/v=dump_extra=freq=keyframe:f=hls:hls_time=10:hls_list_size=6:hls_flags=delete_segments:var_stream_map=\\\'v:0,a:0\\\':hls_segment_filename=' . DOCROOT . 'streams/' . $stream_id . '_%d.ts]"' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -nostdin -hide_banner -loglevel info';
			}
			else if ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_method'] == 3) {
				$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/dehash/bin/ffmpeg ' . ($stream_db['stream_native_frame'] == 1 ? '-re' : '') . ' ' . ($stream_db['stream_http_proxy'] != NULL ? '-http_proxy ' . $stream_db['stream_http_proxy'] : '') . ' -user_agent "' . $stream_db['stream_user_agent'] . '" -copytb 1 -correct_ts_overflow 0 -avoid_negative_ts disabled ' . ($stream_db['stream_format_flags'] != '' ? ' -fflags ' . $stream_db['stream_format_flags'] : '') . ' -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -recv_buffer_size 67108864 -i ' . trim($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) . ' -filter_complex "[v:0]cvdelogo=filename=' . DOCROOT . 'image/' . $obf_DRIQCRxcMhoLMwkaJh8JDTs1FCIhNyI . ':buffer_queue_size=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_buffer_queue_size'] . ':detect_interval=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_dedect_interval'] . ':score_min=' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_score_min'] . ':scale_min=' . $obf_DTY3MzU5PBUhPlwHMi0uITMzGy8zJhE[0] . ':scale_max=' . $obf_DTY3MzU5PBUhPlwHMi0uITMzGy8zJhE[1] . ':padding_left=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[0] . ':padding_right=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[1] . ':padding_top=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[2] . ':padding_bottom=' . $obf_DSguMgMJCwWOA8MMwQtIikDFCkzEzI[3] . '[cvdelogo]; [cvdelogo]split=outputs=1[map:v:0]" -map [map:v:0] -c:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_encoder'] . ' -flags:v +global_header+cgop -preset:v ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_preset'] . ' -g 60 ' . ($obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI != 'auto' ? '-b:v:0 ' . $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI . 'k -maxrate:v:0 ' . $obf_DVwCEjArQCgbDDEyPjk9AQEhFhQDIzI . 'k -bufsize:v:0 ' . $obf_DQEGEyEOLhAfKDgYNDYLAz8XGx4yKyI . 'k' : '') . ' -map a:0 ' . ($obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_acodec'] != 'copy' ? '-c:a ' . $obf_DQUjDFwaMDIOCSgGJCgbBCEXKCQyJAE[0]['hashcode_acodec'] . ' -ar 44100 -b:a:0 128k' : '-c:a copy') . ' -max_muxing_queue_size 512 -f tee "[select=\\\'v:0,a:0\\\':bsfs/v=dump_extra=freq=keyframe:f=hls:hls_time=10:hls_list_size=6:hls_flags=delete_segments:var_stream_map=\\\'v:0,a:0\\\':hls_segment_filename=' . DOCROOT . 'streams/' . $stream_id . '_%d.ts]"' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -nostdin -hide_banner -loglevel info';
			}
		}

		file_put_contents(DOCROOT . 'tmp/' . $stream_id . '_ffmpeg.txt', $obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI);
		$obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI = shell_exec($obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI . ' >> /home/xapicode/iptv_xapicode/streams/' . $stream_id . '_out.log 2>>/home/xapicode/iptv_xapicode/streams/' . $stream_id . '_error.log & echo $!');

		if (!empty($obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI)) {
			file_put_contents(DOCROOT . 'streams/' . $stream_id . '_checker', $obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI);
			return true;
		}
	}
}

function ffmpeg_local_command($stream_id, $stream_db)
{
	global $db;

	if (0 < count($stream_db)) {
		$obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE = json_decode($stream_db['stream_play_pool'], true);
		$obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE = $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE[$stream_db['stream_play_pool_id']];
		$obf_DR8RBCI1FTYYAVwsOzkLLhwZKjs5ODI = json_decode($stream_db['stream_play_pool'], true);
		$obf_DR8RBCI1FTYYAVwsOzkLLhwZKjs5ODI = $obf_DR8RBCI1FTYYAVwsOzkLLhwZKjs5ODI[$stream_db['stream_play_pool_id']];
		$obf_DSYzAQ45PxE2IR48IzwWIhQmGyUtEBE = explode('/', $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE);

		if ($obf_DSYzAQ45PxE2IR48IzwWIhQmGyUtEBE[3] != 'records') {
			if ($handle = opendir($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)) {
				$obf_DSQSDiZAKiUUBA00XDMXNw8YPlwVDSI = [];

				while (false !== $entry = readdir($handle)) {
					if (($entry != '.') && ($entry != '..') && ($entry != $stream_id . '.txt')) {
						array_push($obf_DSQSDiZAKiUUBA00XDMXNw8YPlwVDSI, $entry);
					}
				}

				shuffle($obf_DSQSDiZAKiUUBA00XDMXNw8YPlwVDSI);
				$obf_DRQGJSMzCzMeHSYuDj0eDCg0OCILEQE = '';

				foreach ($obf_DSQSDiZAKiUUBA00XDMXNw8YPlwVDSI as $obf_DRBcBBANLSIEPjE5OxErOC4EEw4bLzI) {
					$obf_DRQGJSMzCzMeHSYuDj0eDCg0OCILEQE = $obf_DRBcBBANLSIEPjE5OxErOC4EEw4bLzI;
				}

				$obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE = $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '/' . $obf_DRQGJSMzCzMeHSYuDj0eDCg0OCILEQE;
			}
		}
		else {
			$obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE = $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE;
		}

		$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 15s ' . DOCROOT . 'bin/ffprobe -i "' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '" -v quiet -print_format json -show_streams 2>&1';
		$obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE = json_decode(shell_exec($obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE), true);
		if (is_array($obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE) && (0 < count($obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE))) {
			if ($stream_db['stream_transcode_id'] == 0) {
				if ($stream_db['stream_concat'] == 1) {
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg -re -auto_convert 1 -f concat -safe 0 -err_detect ignore_err -copytb 1 -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i ' . $obf_DR8RBCI1FTYYAVwsOzkLLhwZKjs5ODI . '/' . $stream_id . '.txt -c:a aac -ac 2 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
				}
				else {
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/ffmpeg -re -fflags +genpts -y -nostdin -hide_banner -loglevel warning -err_detect ignore_err -nofix_dts -start_at_zero -copyts -vsync 0 -correct_ts_overflow 0 -avoid_negative_ts disabled -i "' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '" -vcodec copy  -scodec copy -acodec copy -individual_header_trailer 0 -f segment -segment_format mpegts -segment_time 10 -segment_list_size 6 -segment_format_options mpegts_flags=+initial_discontinuity:mpegts_copyts=1 -segment_list_type m3u8 -segment_list_flags +live+delete -segment_list ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 ' . DOCROOT . 'streams/' . $stream_id . '_%d.ts';
				}
			}
			else {
				$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = set_transcoding_profile($stream_db['stream_transcode_id'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['width'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['height']);
				$obf_DR4bBSw1WzUzQAknGBwKPC0NFDYiByI = set_transcoding_method($stream_db['stream_transcode_id']);

				if ($obf_DR4bBSw1WzUzQAknGBwKPC0NFDYiByI == 'cpu') {
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/ffmpeg -re -fflags +genpts -y -nostdin -hide_banner -loglevel error -nofix_dts -start_at_zero -copyts -vsync 0 -correct_ts_overflow 0 -avoid_negative_ts disabled -i "' . $obf_DSQSDiZAKiUUBA00XDMXNw8YPlwVDSI . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -individual_header_trailer 0 -f segment -segment_format mpegts -segment_time 10 -segment_list_size 6 -segment_format_options mpegts_flags=+initial_discontinuity:mpegts_copyts=1 -segment_list_type m3u8 -segment_list_flags +live+delete -segment_list ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 ' . DOCROOT . 'streams/' . $stream_id . '_%d.ts';
				}
				else {
					$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/nvenc/ffmpeg -y -loglevel error -user_agent "' . $stream_db['stream_user_agent'] . '" -hwaccel cuvid -hwaccel_device ' . $stream_db['stream_transcode_gpu_id'] . ' -c:v ' . set_stream_transcoding_cuvid($stream_db['stream_transcode_id']) . ' -gpu ' . $stream_db['stream_transcode_gpu_id'] . ' -i "' . $obf_DSQSDiZAKiUUBA00XDMXNw8YPlwVDSI . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' ' . (set_transcoding_resolution($stream_db['stream_transcode_id']) != '' ? ' -resize ' . set_transcoding_resolution($stream_db['stream_transcode_id']) : '') . '  -gpu ' . $stream_db['stream_transcode_gpu_id'] . ' -hls_flags delete_segments -hls_time 5 -hls_list_size 10 -f hls ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8';
				}
			}

			$obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI = shell_exec($obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI . ' > /dev/null 2>&1 & echo $!');

			if (!empty($obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI)) {
				$obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE = json_decode($stream_db['stream_status'], true);
				$obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE[0][SERVER] = 1;
				$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_status' => json_encode($obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE), 'stream_id' => $stream_id];
				$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_status = :stream_status WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
				$obf_DR0zMBYhJBAkMQUMPScsGzc3LTQmPwE = ['stream_pid' => $obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI, 'stream_start_time' => time(), 'stream_id' => $stream_id, 'server_id' => SERVER];
				$obf_DTAmNzcoIgsMSQ8Nw0dMDAQBxcHMjI = $db->query('INSERT INTO cms_stream_sys (stream_pid, stream_start_time, stream_id, server_id) VALUES(:stream_pid, :stream_start_time, :stream_id, :server_id)', $obf_DR0zMBYhJBAkMQUMPScsGzc3LTQmPwE);
				return true;
			}
			else {
				$obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE = json_decode($stream_db['stream_status'], true);
				$obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE[0][SERVER] = 0;
				$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_status' => json_encode($obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE), 'stream_id' => $stream_id];
				$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_status = :stream_status WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
				return false;
			}
		}
		else {
			$obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE = json_decode($stream_db['stream_status'], true);
			$obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE[0][SERVER] = 0;
			$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_status' => json_encode($obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE), 'stream_play_pool_id' => $obf_DTYnAz4nMikjBxYVNzk4Py83CQYOPxE, 'stream_id' => $stream_id];
			$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_status = :stream_status, stream_play_pool_id = :stream_play_pool_id WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
			return false;
		}
	}
}

function ffmpeg_youtube_command($stream_id, $stream_db)
{
	global $db;

	if (0 < count($stream_db)) {
		$obf_DR8RBCI1FTYYAVwsOzkLLhwZKjs5ODI = '/home/xapicode/iptv_xapicode/youtube/yt_' . $stream_id;

		if ($stream_db['stream_transcode_id'] == 0) {
			if ($stream_db['stream_concat'] == 1) {
				$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg -re -auto_convert 1 -f concat -safe 0 -err_detect ignore_err -copytb 1 -probesize ' . $obf_DQtcPzw9LDUKKhkbEj47BxshMQ4aDwE . ' -analyzeduration ' . $obf_DSgeIhkjKRYOWzweFAsuCg4JWx8WLgE . ' -i ' . $obf_DR8RBCI1FTYYAVwsOzkLLhwZKjs5ODI . '/' . $stream_id . '.txt -c:a aac -ac 2 -f hls -hls_flags delete_segments -hls_time 10 -hls_list_size 6 ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 -hide_banner -loglevel info';
			}
		}
		else {
			$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = set_transcoding_profile($stream_db['stream_transcode_id'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['width'], $obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE['streams'][0]['height']);
			$obf_DR4bBSw1WzUzQAknGBwKPC0NFDYiByI = set_transcoding_method($stream_db['stream_transcode_id']);

			if ($obf_DR4bBSw1WzUzQAknGBwKPC0NFDYiByI == 'cpu') {
				$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/ffmpeg -y -nostdin -hide_banner -loglevel error -nofix_dts -start_at_zero -copyts -vsync 0 -correct_ts_overflow 0 -avoid_negative_ts disabled -i "' . $obf_DSQSDiZAKiUUBA00XDMXNw8YPlwVDSI . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' -individual_header_trailer 0 -f segment -segment_format mpegts -segment_time 10 -segment_list_size 6 -segment_format_options mpegts_flags=+initial_discontinuity:mpegts_copyts=1 -segment_list_type m3u8 -segment_list_flags +live+delete -segment_list ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 ' . DOCROOT . 'streams/' . $stream_id . '_%d.ts';
			}
			else {
				$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/nvenc/ffmpeg -y -loglevel error -user_agent "' . $stream_db['stream_user_agent'] . '" -hwaccel cuvid -hwaccel_device ' . $stream_db['stream_transcode_gpu_id'] . ' -c:v ' . set_stream_transcoding_cuvid($stream_db['stream_transcode_id']) . ' -gpu ' . $stream_db['stream_transcode_gpu_id'] . ' -i "' . $obf_DSQSDiZAKiUUBA00XDMXNw8YPlwVDSI . '" ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' ' . (set_transcoding_resolution($stream_db['stream_transcode_id']) != '' ? ' -resize ' . set_transcoding_resolution($stream_db['stream_transcode_id']) : '') . '  -gpu ' . $stream_db['stream_transcode_gpu_id'] . ' -hls_flags delete_segments -hls_time 5 -hls_list_size 10 -f hls ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8';
			}
		}

		$obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI = shell_exec($obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI . ' > /dev/null 2>&1 & echo $!');

		if (!empty($obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI)) {
			$obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE = json_decode($stream_db['stream_status'], true);
			$obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE[0][SERVER] = 1;
			$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_status' => json_encode($obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE), 'stream_id' => $stream_id];
			$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_status = :stream_status WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
			$obf_DR0zMBYhJBAkMQUMPScsGzc3LTQmPwE = ['stream_pid' => $obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI, 'stream_start_time' => time(), 'stream_id' => $stream_id, 'server_id' => SERVER];
			$obf_DTAmNzcoIgsMSQ8Nw0dMDAQBxcHMjI = $db->query('INSERT INTO cms_stream_sys (stream_pid, stream_start_time, stream_id, server_id) VALUES(:stream_pid, :stream_start_time, :stream_id, :server_id)', $obf_DR0zMBYhJBAkMQUMPScsGzc3LTQmPwE);
			return true;
		}
		else {
			$obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE = json_decode($stream_db['stream_status'], true);
			$obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE[0][SERVER] = 0;
			$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_status' => json_encode($obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE), 'stream_id' => $stream_id];
			$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_status = :stream_status WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
			return false;
		}
	}
	else {
		$obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE = json_decode($stream_db['stream_status'], true);
		$obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE[0][SERVER] = 0;
		$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_status' => json_encode($obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE), 'stream_play_pool_id' => $obf_DTYnAz4nMikjBxYVNzk4Py83CQYOPxE, 'stream_id' => $stream_id];
		$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_status = :stream_status, stream_play_pool_id = :stream_play_pool_id WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
		return false;
	}
}

function record_start($stream_id)
{
	global $db;
	$obf_DVstDT8nOS8YDCwLCQ8RGyQoHRkaFjI = [$stream_id];
	$obf_DRYTEiMwAwcFDjVAIj84CiscKDgwBgE = $db->query('SELECT * FROM cms_streams WHERE stream_id = ?', $obf_DVstDT8nOS8YDCwLCQ8RGyQoHRkaFjI);
	$obf_DStABT8zPRNcLAs9IRA5GiQQOQQcEhE = json_decode($obf_DRYTEiMwAwcFDjVAIj84CiscKDgwBgE[0]['stream_record_path'], true);
	$obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE = json_decode($obf_DRYTEiMwAwcFDjVAIj84CiscKDgwBgE[0]['stream_play_pool'], true);
	$obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE = $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE[$obf_DRYTEiMwAwcFDjVAIj84CiscKDgwBgE[0]['stream_play_pool_id']];
	$obf_DQ8RJyEDQAspHCkyOA8dODBAGh0CCjI = $stream_id . '_' . date('d_m_y_h_i', time());
	$obf_DStABT8zPRNcLAs9IRA5GiQQOQQcEhE[] = $obf_DQ8RJyEDQAspHCkyOA8dODBAGh0CCjI;
	$obf_DQ80IhsRMTgxExktCwc7HVs1GwweMiI = DOCROOT . 'bin/ffmpeg -y -nostdin -hide_banner -loglevel warning -err_detect ignore_err -nofix_dts -start_at_zero -copyts -vsync 0 -correct_ts_overflow 0 -avoid_negative_ts disabled -max_interleave_delta 0 -user_agent "streaminy recording" -i "' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '" -vcodec copy -scodec copy -acodec copy -individual_header_trailer 0 ' . DOCROOT . 'records/' . $obf_DQ8RJyEDQAspHCkyOA8dODBAGh0CCjI . '.ts';
	$obf_DTVcMT07Ly0ICA8RASEQQDw4C1saWxE = shell_exec($obf_DQ80IhsRMTgxExktCwc7HVs1GwweMiI . ' > /dev/null 2>&1 & echo $!');
	$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_record_status' => 1, 'stream_record_pid' => $obf_DTVcMT07Ly0ICA8RASEQQDw4C1saWxE, 'stream_record_path' => json_encode($obf_DStABT8zPRNcLAs9IRA5GiQQOQQcEhE), 'stream_id' => $stream_id];
	$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_record_status = :stream_record_status, stream_record_pid = :stream_record_pid, stream_record_path = :stream_record_path WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
	$obf_DSgkEDc5BTIENTAzMiULIgw3NgwlFBE = ['archive_path' => $obf_DQ8RJyEDQAspHCkyOA8dODBAGh0CCjI, 'archive_server' => SERVER];
	$obf_DQ4NBQ4JPgwSLjwSBg8KKhwSMwkINjI = $db->query('INSERT INTO cms_archive (archive_path, archive_server) VALUES (:archive_path, :archive_server)', $obf_DSgkEDc5BTIENTAzMiULIgw3NgwlFBE);
}

function record_stop($stream_id)
{
	global $db;
	$obf_DVstDT8nOS8YDCwLCQ8RGyQoHRkaFjI = [$stream_id];
	$obf_DRYTEiMwAwcFDjVAIj84CiscKDgwBgE = $db->query('SELECT * FROM cms_streams WHERE stream_id = ?', $obf_DVstDT8nOS8YDCwLCQ8RGyQoHRkaFjI);
	posix_kill($obf_DRYTEiMwAwcFDjVAIj84CiscKDgwBgE[0]['stream_record_pid'], 9);
	$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_record_status' => 0, 'stream_record_pid' => NULL, 'stream_id' => $stream_id];
	$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_record_status = :stream_record_status, stream_record_pid = :stream_record_pid WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
}

function start_loop_stream($stream_id, $server_ip, $server_port)
{
	global $db;
	$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_loop_to_status' => 6, 'stream_id' => $stream_id];
	$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_loop_to_status = :stream_loop_to_status WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
	$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/ffmpeg -y -nostdin -hide_banner -loglevel warning -err_detect ignore_err -nofix_dts -start_at_zero -copyts -vsync 0 -correct_ts_overflow 0 -avoid_negative_ts disabled -max_interleave_delta 0 -user_agent "streaminy looper" -i "http://' . $server_ip . ':' . $server_port . '/live/loop/loop/' . $stream_id . '.ts" -vcodec copy -scodec copy -acodec copy -individual_header_trailer 0 -f segment -segment_format mpegts -segment_time 10 -segment_list_size 6 -segment_format_options mpegts_flags=+initial_discontinuity:mpegts_copyts=1 -segment_list_type m3u8 -segment_list_flags +live+delete -segment_list ' . DOCROOT . 'streams/' . $stream_id . '_.m3u8 ' . DOCROOT . 'streams/' . $stream_id . '_%d.ts';
	$obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI = shell_exec($obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI . ' > /dev/null 2>&1 & echo $!');

	if (!empty($obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI)) {
		file_put_contents(DOCROOT . 'streams/' . $stream_id . '_checker', $obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI);
		return true;
	}
}

function start_live_stream($stream_db, $stream_id, $binary_id, $hashcode_id, $status, $stream_loop = 0)
{
	switch ($status) {
	case 0:
		if ($binary_id == 1) {
			if (ffmpeg_live_command($stream_id, 10, $stream_loop, $stream_db)) {
				return true;
			}
		}

		break;
	case 3:
		if ($binary_id == 1) {
			if (ffmpeg_live_command($stream_id, 10, $stream_loop, $stream_db)) {
				return true;
			}
		}

		break;
	case 4:
		stop_stream($stream_db, $stream_id, $binary_id, $hashcode_id, 4, $stream_loop);

		if ($binary_id == 1) {
			if (ffmpeg_live_command($stream_id, 10, $stream_loop, $stream_db)) {
				return true;
			}
		}

		break;
	}
}

function start_adaptive_stream($stream_db, $stream_id, $stream_adaptive_profile, $status, $stream_loop = 0)
{
	switch ($status) {
	case 0:
		if (ffmpeg_live_command($stream_id, 10, $stream_loop, $stream_db)) {
			return true;
		}

		break;
	case 3:
		if (ffmpeg_live_command($stream_id, 10, $stream_loop, $stream_db)) {
			return true;
		}

		break;
	case 4:
		stop_adaptive_stream($stream_db, $stream_id, $stream_adaptive_profile, 4);

		if ($obf_DTkrGScHHUAiFyFcHzAZWyscXAYpJgE == 1) {
			if (ffmpeg_live_command($stream_id, 10, $stream_loop, $stream_db)) {
				return true;
			}
		}

		break;
	}
}

function start_local_stream($stream_id, $binary_id, $hashcode_id, $status, $stream_db)
{
	global $db;
	delete_sys_if_exists($stream_id);
	if (($status == 0) || ($status == 3)) {
		if ($binary_id == 1) {
			if (ffmpeg_local_command($stream_id, $stream_db)) {
				return true;
			}
		}
	}
	else if ($status == 4) {
		stop_stream($stream_db, $stream_id, $binary_id, $hashcode_id);

		if ($binary_id == 1) {
			if (ffmpeg_local_command($stream_id, $stream_db)) {
				return true;
			}
		}
	}
}

function start_youtube_stream($stream_id, $binary_id, $hashcode_id, $status, $stream_db)
{
	global $db;
	delete_sys_if_exists($stream_id);
	if (($status == 0) || ($status == 3)) {
		if ($binary_id == 1) {
			if (ffmpeg_youtube_command($stream_id, $stream_db)) {
				return true;
			}
		}
	}
	else if ($status == 4) {
		stop_stream($stream_db, $stream_id, $binary_id, $hashcode_id);

		if ($binary_id == 1) {
			if (ffmpeg_youtube_command($stream_id, $stream_db)) {
				return true;
			}
		}
	}
}

function start_transcoding_files($stream_id, $bitrate = 2500, $resolution = '1280:720', $stream_db)
{
	global $db;
	$obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE = json_decode($stream_db['stream_play_pool'], true);
	$obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE = $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE[$stream_db['stream_play_pool_id']];

	if ($handle = opendir($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE)) {
		$obf_DSQSDiZAKiUUBA00XDMXNw8YPlwVDSI = [];

		while (false !== $entry = readdir($handle)) {
			if (($entry != '.') && ($entry != '..') && ($entry != $stream_id . '.txt')) {
				$obf_DSQSDiZAKiUUBA00XDMXNw8YPlwVDSI[] = $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '/' . $entry;
			}
		}

		$obf_DSQGMTIJQBc7JTAEBik4Chc9BSw3HAE = 0;

		foreach ($obf_DSQSDiZAKiUUBA00XDMXNw8YPlwVDSI as $source) {
			$obf_DVwKPxoqNjIyKCsZOzxbPC0aFgYQEBE = reset(json_decode(@shell_exec(DOCROOT . 'bin/ffprobe -v quiet -print_format json -show_format -show_streams ' . $source), true));

			if ($obf_DSQGMTIJQBc7JTAEBik4Chc9BSw3HAE < (int) $obf_DVwKPxoqNjIyKCsZOzxbPC0aFgYQEBE[$obf_DRZbKDEyByYMAT0jLUAjGQgaCzsFhE[$key] + 1]['channels']) {
				$obf_DSQGMTIJQBc7JTAEBik4Chc9BSw3HAE = (int) $obf_DVwKPxoqNjIyKCsZOzxbPC0aFgYQEBE[$obf_DRZbKDEyByYMAT0jLUAjGQgaCzsFhE[$key] + 1]['channels'];
			}
		}

		$obf_DSU3LwYbDR4FDxsGC8RNy0TQBssHSI = '';
		$obf_DScUNx0ECsLBDMCIhMJPDMiIQcaMBE = '';

		foreach ($obf_DSQSDiZAKiUUBA00XDMXNw8YPlwVDSI as $source) {
			$obf_DT4uDDM2HyIQHgQZLy4oLScRCzANLiI = explode('_', $source);

			if (!file_exists($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '/' . $stream_id . '_' . end($obf_DT4uDDM2HyIQHgQZLy4oLScRCzANLiI))) {
				$obf_DSU3LwYbDR4FDxsGC8RNy0TQBssHSI .= DOCROOT . 'bin/ffmpeg -i ' . $source . ' -c:v libx264 -pix_fmt yuv420p -b:v ' . $bitrate . 'k -maxrate ' . $bitrate . 'k -bufsize ' . $bitrate . 'k  -preset veryfast -s 1280x720 -r 25 -c:a aac -b:a 128k -ac 2 -movflags +faststart -threads 6 ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '/' . $stream_id . '_' . end(explode('/', $source)) . PHP_EOL;
				$obf_DSU3LwYbDR4FDxsGC8RNy0TQBssHSI .= 'rm -rf ' . $source . PHP_EOL;
				$obf_DScUNx0ECsLBDMCIhMJPDMiIQcaMBE .= 'file \'' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '/' . $stream_id . '_' . end(explode('/', $source)) . '\'' . PHP_EOL;
			}
			else {
				$obf_DScUNx0ECsLBDMCIhMJPDMiIQcaMBE .= 'file \'' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '/' . end(explode('/', $source)) . '\'' . PHP_EOL;
			}
		}

		file_put_contents('/tmp/' . $stream_id . '.sh', $obf_DSU3LwYbDR4FDxsGC8RNy0TQBssHSI);
		file_put_contents($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '/' . $stream_id . '.txt', $obf_DScUNx0ECsLBDMCIhMJPDMiIQcaMBE);
		$obf_DSYyAyMbFDBbPRo0LT0GDD0cFw0UNzI = shell_exec('sh /tmp/' . $stream_id . '.sh > /dev/null 2>&1 & echo $!');
		$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = false;

		if ($obf_DSYyAyMbFDBbPRo0LT0GDD0cFw0UNzI != '') {
			file_put_contents('/tmp/' . $stream_id . '.txt', $obf_DSYyAyMbFDBbPRo0LT0GDD0cFw0UNzI);
			$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_concat_status' => 1, 'stream_id' => $stream_id];
			$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_concat_status = :stream_concat_status WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
			$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = true;
		}
		else {
			$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = false;
		}

		return $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI;
	}
}

function start_transcoding_youtube($stream_id, $bitrate = 3500, $resolution = '1280:720', $stream_db)
{
	global $db;
	$obf_DSU3LwYbDR4FDxsGC8RNy0TQBssHSI = '';
	$obf_DScUNx0ECsLBDMCIhMJPDMiIQcaMBE = '';
	shell_exec('rm -rf /home/xapicode/iptv_xapicode/streams/' . $stream_id . '_*');

	if (!file_exists('/home/xapicode/iptv_xapicode/youtube/yt_' . $stream_id)) {
		mkdir('/home/xapicode/iptv_xapicode/youtube/yt_' . $stream_id, 511);
	}

	$obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE = '/home/xapicode/iptv_xapicode/youtube/yt_' . $stream_id;
	$obf_DR85GBMVHwcdITwjEDYlQDcECRQVLCI = json_decode($stream_db['stream_play_pool'], true);

	foreach ($obf_DR85GBMVHwcdITwjEDYlQDcECRQVLCI as $obf_DTIoIgEeGRUQBCoTMzQ8FwM9Bzg5MSI) {
		$obf_DSgtGxgqIxYLTEMGhYFGxYDls5CwE = explode('watch?', $obf_DTIoIgEeGRUQBCoTMzQ8FwM9Bzg5MSI);
		$obf_DSgtGxgqIxYLTEMGhYFGxYDls5CwE = str_replace('v=', '', $obf_DSgtGxgqIxYLTEMGhYFGxYDls5CwE[1]);

		if (!file_exists($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '/' . $stream_id . '_' . $obf_DSgtGxgqIxYLTEMGhYFGxYDls5CwE . '.mp4')) {
			$obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI = trim(shell_exec(DOCROOT . 'bin/youtube-dl -4 -f best -g ' . $obf_DTIoIgEeGRUQBCoTMzQ8FwM9Bzg5MSI));
			$obf_DSU3LwYbDR4FDxsGC8RNy0TQBssHSI .= DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg -y -i "' . $obf_DSQeDy0IDlsMDwsVB1sRHh0tHjgXHDI . '" -c:v libx264 -pix_fmt yuv420p -b:v ' . $bitrate . 'k -maxrate ' . $bitrate . 'k -bufsize ' . $bitrate . 'k  -preset veryfast -s 1280x720 -r 25 -c:a aac -b:a 128k -ac 2 -movflags +faststart -threads 6 ' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '/' . $stream_id . '_' . $obf_DSgtGxgqIxYLTEMGhYFGxYDls5CwE . '.mp4' . PHP_EOL;
			$obf_DScUNx0ECsLBDMCIhMJPDMiIQcaMBE .= 'file \'' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '/' . $stream_id . '_' . $obf_DSgtGxgqIxYLTEMGhYFGxYDls5CwE . '.mp4\'' . PHP_EOL;
		}
		else {
			$obf_DScUNx0ECsLBDMCIhMJPDMiIQcaMBE .= 'file \'' . $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '/' . $stream_id . '_' . $obf_DSgtGxgqIxYLTEMGhYFGxYDls5CwE . '.mp4\'' . PHP_EOL;
		}
	}

	file_put_contents('/tmp/' . $stream_id . '.sh', $obf_DSU3LwYbDR4FDxsGC8RNy0TQBssHSI);
	file_put_contents($obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE . '/' . $stream_id . '.txt', $obf_DScUNx0ECsLBDMCIhMJPDMiIQcaMBE);
	$obf_DSYyAyMbFDBbPRo0LT0GDD0cFw0UNzI = shell_exec('sh /tmp/' . $stream_id . '.sh > /dev/null 2>&1 & echo $!');
	$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = false;

	if ($obf_DSYyAyMbFDBbPRo0LT0GDD0cFw0UNzI != '') {
		file_put_contents('/tmp/' . $stream_id . '.txt', $obf_DSYyAyMbFDBbPRo0LT0GDD0cFw0UNzI);
		$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_concat_status' => 1, 'stream_id' => $stream_id];
		$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_concat_status = :stream_concat_status WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
		$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = true;
	}
	else {
		$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = false;
	}

	return $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI;
}

function stop_stream($stream_db, $stream_id, $binary_id, $hashcode_id, $status = '', $stream_loop = 0)
{
	global $db;

	if (posix_kill($stream_db['stream_pid'], 0)) {
		$obf_DTAoDigZMwgPLwUcNwYRHBYQMBk8AjI = shell_exec('ps -p ' . $stream_db['stream_pid'] . ' -o comm=');

		if (trim($obf_DTAoDigZMwgPLwUcNwYRHBYQMBk8AjI) == 'ffmpeg') {
			posix_kill($stream_db['stream_pid'], 9);
		}
	}

	delete_sys_if_exists($stream_id);
	delete_stream_data($stream_id);

	if ($status != 4) {
		update_stream_status($stream_id, 2, $stream_loop);
	}

	return true;
}

function stop_adaptive_stream($stream_db, $stream_id, $stream_adaptive_profile)
{
	global $db;

	if (posix_kill($stream_db['stream_pid'], 0)) {
		$obf_DTAoDigZMwgPLwUcNwYRHBYQMBk8AjI = shell_exec('ps -p ' . $stream_db['stream_pid'] . ' -o comm=');

		if (trim($obf_DTAoDigZMwgPLwUcNwYRHBYQMBk8AjI) == 'ffmpeg') {
			posix_kill($stream_db['stream_pid'], 9);
		}
	}

	delete_sys_if_exists($stream_id);
	delete_stream_data($stream_id);

	if ($status != 4) {
		update_stream_status($stream_id, 2, 0);
	}

	return true;
}

function offline_stream($stream_id, $stream_method, $stream_play_pool = 0, $stream_play_pool_id = 0, $stream_loop = 0)
{
	if ($stream_method == 1) {
		global $db;
		$obf_DQY8EAQJDQkXChUSPEADCMiKB8uERE = json_decode($stream_play_pool, true);
		$obf_DQY8EAQJDQkXChUSPEADCMiKB8uERE = count($obf_DQY8EAQJDQkXChUSPEADCMiKB8uERE) - 1;

		if ($stream_play_pool_id < $obf_DQY8EAQJDQkXChUSPEADCMiKB8uERE) {
			$obf_DS0zNj8xCT0ZCioNLAQsGykUByoRIgE = $stream_play_pool_id + 1;
		}
		else {
			$obf_DS0zNj8xCT0ZCioNLAQsGykUByoRIgE = 0;
		}

		$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_play_pool_id' => $obf_DS0zNj8xCT0ZCioNLAQsGykUByoRIgE, 'stream_id' => $stream_id];
		$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_play_pool_id = :stream_play_pool_id WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
	}

	write_stream_log($stream_id);
	delete_stream_data($stream_id);
	update_stream_status($stream_id, 0, $stream_loop);
}

function get_pid_of_stream($stream_id)
{
	global $db;
	$obf_DQYQHigfOBY0GScbLDw3GxspFSQpKgE = [$stream_id, SERVER];
	$obf_DTMIPCgSAgUSKScHIgE5HAMOGRoyPwE = $db->query('SELECT stream_pid FROM cms_stream_sys WHERE stream_id = ? AND server_id = ?', $obf_DQYQHigfOBY0GScbLDw3GxspFSQpKgE);

	if (0 < count($obf_DTMIPCgSAgUSKScHIgE5HAMOGRoyPwE)) {
		$obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI = $obf_DTMIPCgSAgUSKScHIgE5HAMOGRoyPwE[0]['stream_pid'];
	}
	else {
		$obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI = 0;
	}

	return $obf_DQoMGTZbJz4wNwojJwIzMwYmBBkXDjI;
}

function get_start_time_of_stream($stream_id)
{
	global $db;
	$obf_DQYQHigfOBY0GScbLDw3GxspFSQpKgE = [$stream_id, SERVER];
	$obf_DTMIPCgSAgUSKScHIgE5HAMOGRoyPwE = $db->query('SELECT stream_start_time FROM cms_stream_sys WHERE stream_id = ? AND server_id = ?', $obf_DQYQHigfOBY0GScbLDw3GxspFSQpKgE);

	if (0 < count($obf_DTMIPCgSAgUSKScHIgE5HAMOGRoyPwE)) {
		$obf_DSsiOwUxBDIpEzUUBx4KNRIhIw0SMDI = $obf_DTMIPCgSAgUSKScHIgE5HAMOGRoyPwE[0]['stream_start_time'];
	}
	else {
		$obf_DSsiOwUxBDIpEzUUBx4KNRIhIw0SMDI = 0;
	}

	return $obf_DSsiOwUxBDIpEzUUBx4KNRIhIw0SMDI;
}

function write_stream_log($stream_id)
{
	global $db;
	$error_log = file('/home/xapicode/iptv_xapicode/streams/' . $stream_id . '_error.log');
	$log = array_slice($error_log, -3, 3, true);
	$obf_DVstDT8nOS8YDCwLCQ8RGyQoHRkaFjI = [json_encode($log), $stream_id];
	$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_log = ? WHERE stream_id = ?', $obf_DVstDT8nOS8YDCwLCQ8RGyQoHRkaFjI);
}

function delete_stream_data($stream_id)
{
	global $db;
	$obf_DT1bWyoQLgkpGwEyJi4pKARbGgg2HRE = '/bin/rm -r ' . DOCROOT . 'streams/' . $stream_id . '_*';
	shell_exec($obf_DT1bWyoQLgkpGwEyJi4pKARbGgg2HRE . ' > /dev/null 2>&1');
	delete_sys_if_exists($stream_id);
	kill_activity_of_stream($stream_id);
}

function delete_sys_if_exists($stream_id)
{
	global $db;
	$obf_DQoLDiodIxkGEh4RJg8IFjkZPxo7PBE = [$stream_id, SERVER];
	$obf_DQE2GjUJCQYBKiIoOBIaNwEQLj1cLBE = $db->query('DELETE FROM cms_stream_sys WHERE stream_id = ? AND server_id = ?', $obf_DQoLDiodIxkGEh4RJg8IFjkZPxo7PBE);
}

function kill_activity_of_stream($stream_id)
{
	global $db;
	$obf_DR4PFx0nNyZcDSYvGwYcCRUBXDAqGBE = [$stream_id, SERVER];
	$obf_DT5AEh8pWx0EQDclFSgzIi04Hws8BjI = $db->query('SELECT stream_activity_id, stream_activity_php_pid FROM cms_stream_activity WHERE stream_activity_stream_id = ? AND stream_activity_server_id = ?', $obf_DR4PFx0nNyZcDSYvGwYcCRUBXDAqGBE);

	if (0 < count($obf_DT5AEh8pWx0EQDclFSgzIi04Hws8BjI)) {
		foreach ($obf_DT5AEh8pWx0EQDclFSgzIi04Hws8BjI as $obf_DSkqPDQbECUaEwoEAVsDGxszNj4vJSI) {
			$obf_DQUiGjAhMT0rMA0WChw9HC8pMS89CTI = $obf_DSkqPDQbECUaEwoEAVsDGxszNj4vJSI['stream_activity_php_pid'];

			if (posix_kill($obf_DQUiGjAhMT0rMA0WChw9HC8pMS89CTI, 0)) {
				$obf_DTAoDigZMwgPLwUcNwYRHBYQMBk8AjI = shell_exec('ps -p ' . $obf_DQUiGjAhMT0rMA0WChw9HC8pMS89CTI . ' -o comm=');

				if (trim($obf_DTAoDigZMwgPLwUcNwYRHBYQMBk8AjI) != 'ffmpeg') {
					posix_kill($obf_DQUiGjAhMT0rMA0WChw9HC8pMS89CTI, 9);
				}

				$obf_DSYmDQgRBD0LFBoNywcIRgGEg5cGxE = [$obf_DSkqPDQbECUaEwoEAVsDGxszNj4vJSI['stream_activity_id']];
				$obf_DSw9DSYyCRosGSoiBykDKRYoGzg3LDI = $db->query('DELETE FROM cms_stream_activity WHERE stream_activity_id = ?', $obf_DSYmDQgRBD0LFBoNywcIRgGEg5cGxE);
				$delete_connection = '/bin/rm -r ' . DOCROOT . 'tmp/' . $obf_DSkqPDQbECUaEwoEAVsDGxszNj4vJSI['stream_activity_id'] . '.con';
				shell_exec($delete_connection . ' > /dev/null 2>&1');
			}
		}
	}
}

function update_stream_status($stream_id, $status, $stream_loop)
{
	global $db;

	if ($stream_loop != 1) {
		$obf_DVstDT8nOS8YDCwLCQ8RGyQoHRkaFjI = [$stream_id];
		$obf_DRYTEiMwAwcFDjVAIj84CiscKDgwBgE = $db->query('SELECT stream_status, stream_is_demand FROM cms_streams WHERE stream_id = ?', $obf_DVstDT8nOS8YDCwLCQ8RGyQoHRkaFjI);
		$obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE = json_decode($obf_DRYTEiMwAwcFDjVAIj84CiscKDgwBgE[0]['stream_status'], true);

		if ($obf_DRYTEiMwAwcFDjVAIj84CiscKDgwBgE[0]['stream_is_demand'] == 1) {
			$obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE[0][SERVER] = 2;
		}
		else {
			$obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE[0][SERVER] = $status;
		}

		$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_status' => json_encode($obf_DRNAFwcDOwE7WxtbBAIlKBY7FyU3GwE), 'stream_loop_to_status' => 2, 'stream_id' => $stream_id];
		$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_status = :stream_status, stream_loop_to_status = :stream_loop_to_status WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
		delete_sys_if_exists($stream_id);
	}
	else {
		$obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI = ['stream_loop_from_status' => $status, 'stream_loop_to_status' => 5, 'stream_id' => $stream_id];
		$obf_DSgOHgoBB8LKgEsGhgtGjZbHQgvBzI = $db->query('UPDATE cms_streams SET stream_loop_from_status = :stream_loop_from_status, stream_loop_to_status = :stream_loop_to_status WHERE stream_id = :stream_id', $obf_DT0xGlsyPjg0EyQULjEHPRIbBj89MCI);
		delete_sys_if_exists($stream_id);
	}
}

function update_stream_information($stream_id, $adaptive = false)
{
	global $db;

	if (!$adaptive) {
		$obf_DS5AFgUOMBABJBIvJigzORYzJkAOGhE = stream_segments($stream_id);

		if ($obf_DS5AFgUOMBABJBIvJigzORYzJkAOGhE) {
			$last_segment = DOCROOT . 'streams/' . end($obf_DS5AFgUOMBABJBIvJigzORYzJkAOGhE);

			if (file_exists($last_segment)) {
				$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 5s ' . DOCROOT . 'bin/ffprobe -i ' . $last_segment . ' -v quiet -probesize 50000 -analyzeduration 50000 -print_format json -show_format -show_streams &';
				$obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE = shell_exec($obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE);
				$obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE = json_decode($obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE, true);
				if (is_array($obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE) && (0 < count($obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE))) {
					$obf_DQomChkMJgEbBxYoJxEaFwcYJyEjBiI = ['width' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['width'], 'height' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['height'], 'vcodec' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['codec_name'], 'acodec' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][1]['codec_name'], 'framerate' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['r_frame_rate'], 'kbps' => (int) $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['format']['size'] / $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['format']['duration'] / 1024];
					file_put_contents(DOCROOT . 'streams/' . $stream_id . '_prob', json_encode($obf_DQomChkMJgEbBxYoJxEaFwcYJyEjBiI));
					$obf_DRMZKCsiEAgFy4bBgsjPz45HBwEAxE = file_get_contents(DOCROOT . 'streams/' . $stream_id . '_prob');
					$obf_DRMZKCsiEAgFy4bBgsjPz45HBwEAxE = json_decode($obf_DRMZKCsiEAgFy4bBgsjPz45HBwEAxE, true);
					$speed = '0x';

					if ($obf_DRMZKCsiEAgFy4bBgsjPz45HBwEAxE['kbps'] != 0) {
						$speed = sprintf('%0.2f', (int) $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['format']['size'] / $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['format']['duration'] / 1024 / $obf_DRMZKCsiEAgFy4bBgsjPz45HBwEAxE['kbps']) . 'x';
					}

					$data_array = ['width' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['width'] ? $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['width'] : '', 'height' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['height'] ? $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['height'] : '', 'vcodec' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['codec_name'] ? $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['codec_name'] : '', 'acodec' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][1]['codec_name'] ? $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][1]['codec_name'] : '', 'framerate' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['r_frame_rate'] ? $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['r_frame_rate'] : '', 'kbps' => (int) $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['format']['size'] / $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['format']['duration'] / 1024, 'speed' => $speed];
					$obf_DRQzKh48HiIxPTM7BCsWPjZcKwM2JxE = ['stream_data' => json_encode($data_array), 'stream_bitrate' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['format']['bit_rate'] / 1024, 'stream_id' => $stream_id, 'server_id' => SERVER];
					$obf_DR8PFQYbIhUKO1wDNCoxFg82JRsfJRE = $db->query('UPDATE cms_stream_sys SET stream_data = :stream_data, stream_bitrate = :stream_bitrate WHERE stream_id = :stream_id AND server_id = :server_id', $obf_DRQzKh48HiIxPTM7BCsWPjZcKwM2JxE);
					return true;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	else {
		$obf_DS5AFgUOMBABJBIvJigzORYzJkAOGhE = stream_segment_of_adaptive($stream_id, $adaptive);
		$obf_DS5AFgUOMBABJBIvJigzORYzJkAOGhE = json_decode($obf_DS5AFgUOMBABJBIvJigzORYzJkAOGhE, true);
		$data_array = [];

		foreach ($obf_DS5AFgUOMBABJBIvJigzORYzJkAOGhE as $segment) {
			$last_segment = DOCROOT . 'streams/' . $segment;
			$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 5s ' . DOCROOT . 'bin/ffprobe -i ' . $last_segment . ' -v quiet -probesize 50000 -analyzeduration 50000 -print_format json -show_format -show_streams &';
			$obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE = shell_exec($obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE);
			$obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE = json_decode($obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE, true);
			if (is_array($obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE) && (0 < count($obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE))) {
				$obf_DVs7OywkFj8YPwgTMh47BjsWCR8JBwE = [$stream_id, SERVER];
				$obf_DR4UJh43M1wOEAkOFDglMgwHMBwZDgE = $db->query('SELECT stream_sys_id, stream_data FROM cms_stream_sys WHERE stream_id = ? AND server_id = ?', $obf_DVs7OywkFj8YPwgTMh47BjsWCR8JBwE);
				$obf_DRMZKCsiEAgFy4bBgsjPz45HBwEAxE = json_decode($obf_DR4UJh43M1wOEAkOFDglMgwHMBwZDgE[0]['stream_data'], true);
				$speed = '0x';

				if ($obf_DRMZKCsiEAgFy4bBgsjPz45HBwEAxE['kbps'] != 0) {
					$speed = sprintf('%0.2f', (int) $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['format']['size'] / $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['format']['duration'] / 1024 / $obf_DRMZKCsiEAgFy4bBgsjPz45HBwEAxE['kbps']) . 'x';
				}

				$data_array[] = ['width' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['width'], 'height' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['height'], 'vcodec' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['codec_name'], 'acodec' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][1]['codec_name'], 'framerate' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['streams'][0]['r_frame_rate'], 'kbps' => $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['format']['bit_rate'] / 1024];
			}
		}

		if (0 < count($obf_DVs7OywkFj8YPwgTMh47BjsWCR8JBwE)) {
			$obf_DRQzKh48HiIxPTM7BCsWPjZcKwM2JxE = ['stream_data' => json_encode($data_array), 'stream_id' => $stream_id, 'server_id' => SERVER];
			$obf_DR8PFQYbIhUKO1wDNCoxFg82JRsfJRE = $db->query('UPDATE cms_stream_sys SET stream_data = :stream_data WHERE stream_id = :stream_id AND server_id = :server_id', $obf_DRQzKh48HiIxPTM7BCsWPjZcKwM2JxE);
		}
	}
}

function check_fingerprint($line_id, $server_id)
{
	global $db;
	$obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE = [$line_id, 0, time()];
	$obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI = $db->query('SELECT line_id, line_fingerprint FROM cms_lines WHERE line_id = ? AND line_fingerprint_start_time != ? AND line_fingerprint_start_time < ?', $obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE);

	if (0 < count($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI)) {
		$obf_DSscGygYHA5APR88NzsaIRoxHR8BFAE = json_decode($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_fingerprint'], true);

		if ($obf_DSscGygYHA5APR88NzsaIRoxHR8BFAE[0][$server_id] == 1) {
			return true;
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}
}

function start_fingerprint($search_segment_id, $read_segment_id, $stream_id, $line_id, $line_user, $stream_method, $stream_adaptive_profile)
{
	global $db;
	$obf_DSclHicvBiU2DRokMiEdQD05JBgBAgE = '/bin/rm -r ' . DOCROOT . 'streams/' . $stream_id . '_fingerprint_' . $line_id . '.ts';
	shell_exec($obf_DSclHicvBiU2DRokMiEdQD05JBgBAgE);
	$obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE = [$line_id, $stream_id];
	$obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI = $db->query('SELECT line_id, line_expire_date, line_fingerprint_typ, line_fingerprint_custom_text FROM cms_lines WHERE line_id = ? AND line_fingerprint_stream_id = ?', $obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE);

	if (0 < count($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI)) {
		$obf_DRE0DBocDCssJTMzK1wXNis7DAQQMDI = $stream_id . '_' . $read_segment_id . '.ts';
		$obf_DQQXBDgiK1wTGyspDBYxMB83ODAxPxE = $stream_id . '_fingerprint_' . $line_id . '.ts';

		if ($stream_method == 5) {
			$obf_DRcNHB4OLQ4xMC09HiY4ASkHAygWCRE = explode('_', $obf_DRE0DBocDCssJTMzK1wXNis7DAQQMDI);

			while (!file_exists(DOCROOT . 'streams/' . $stream_id . '_' . $obf_DRcNHB4OLQ4xMC09HiY4ASkHAygWCRE[1] . '_' . $search_segment_id . '.ts')) {
				usleep(1000);
			}
		}
		else {
			while (!file_exists(DOCROOT . 'streams/' . $stream_id . '_' . $search_segment_id . '.ts')) {
				usleep(1000);
			}
		}

		$obf_DQYQHigfOBY0GScbLDw3GxspFSQpKgE = [$stream_id, SERVER];
		$obf_DTMIPCgSAgUSKScHIgE5HAMOGRoyPwE = $db->query('SELECT stream_bitrate, stream_data from cms_stream_sys WHERE stream_id = ? AND server_id = ?', $obf_DQYQHigfOBY0GScbLDw3GxspFSQpKgE);

		if ($stream_method == 5) {
			$obf_DRcNHB4OLQ4xMC09HiY4ASkHAygWCRE = explode('_', $obf_DRE0DBocDCssJTMzK1wXNis7DAQQMDI);
			$obf_DTEkNQc7JQsfARgBXDEGBhgTLQgNiI = json_decode($stream_adaptive_profile, true);
			$obf_DSgfKAssBDMRGlwXFhgkAj00HxwoLAE = '';
			$j = 0;

			foreach ($obf_DTEkNQc7JQsfARgBXDEGBhgTLQgNiI as $obf_DSoMOSwaCjgXBSEoCgQDCRESPzwZDCI => $obf_DQc9HAgbBQI4JAZAASsGFycmNS88CDI) {
				if (('_' . $obf_DRcNHB4OLQ4xMC09HiY4ASkHAygWCRE[1]) == $obf_DSoMOSwaCjgXBSEoCgQDCRESPzwZDCI) {
					$obf_DSgfKAssBDMRGlwXFhgkAj00HxwoLAE = $j;
				}

				$j++;
			}

			$obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE = json_decode($obf_DTMIPCgSAgUSKScHIgE5HAMOGRoyPwE[0]['stream_data'], true);
			$obf_DVw2NSUBLD0aOBoJGQcrMxBANBMoOCI = (int) 150 * ($obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE[$obf_DSgfKAssBDMRGlwXFhgkAj00HxwoLAE]['width'] / 1920);
			$obf_DRYKj4OGg85KTA9EA0kDRUpLS08MgE = $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE[$obf_DQYEGiQPOzYzFxwhPB8DNCYBDAoJPyI]['height'] - (int) 250 * ($obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE[$obf_DSgfKAssBDMRGlwXFhgkAj00HxwoLAE]['height'] / 1080) - rand(0, 20);
			$fontsize = (int) 36 * ($obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE[$obf_DSgfKAssBDMRGlwXFhgkAj00HxwoLAE]['width'] / 1920);
			$streambitrate = round($obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE[$obf_DSgfKAssBDMRGlwXFhgkAj00HxwoLAE]['kbps']);
		}
		else {
			$obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE = json_decode($obf_DTMIPCgSAgUSKScHIgE5HAMOGRoyPwE[0]['stream_data'], true);
			$obf_DVw2NSUBLD0aOBoJGQcrMxBANBMoOCI = (int) 150 * ($obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['width'] / 1920);
			$obf_DRYKj4OGg85KTA9EA0kDRUpLS08MgE = $obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['height'] - (int) 250 * ($obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['height'] / 1080) - rand(0, 20);
			$fontsize = (int) 36 * ($obf_DQsoIRomHRYlI1sIGhoMMT4UOAwGEBE['width'] / 1920);
			$streambitrate = $obf_DTMIPCgSAgUSKScHIgE5HAMOGRoyPwE[0]['stream_bitrate'];
		}

		if ($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_fingerprint_typ'] == 0) {
			$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg -y -i ' . DOCROOT . 'streams/' . $obf_DRE0DBocDCssJTMzK1wXNis7DAQQMDI . ' -vf drawbox="x=0:y=' . ($obf_DRYKj4OGg85KTA9EA0kDRUpLS08MgE - 25) . ':w=in_w:h=80:color=black@0.5:t=fill",drawtext="/home/xapicode/iptv_xapicode/fonts/Oswald-Bold.ttf:fontcolor=white:fontsize=' . $fontsize . ':text=USER ' . $line_user . ':x=(w-tw)/2:y=' . $obf_DRYKj4OGg85KTA9EA0kDRUpLS08MgE . '" -c:v libx264 -b:v ' . $streambitrate . 'k -preset ultrafast -c:a copy -muxdelay 0 ' . DOCROOT . 'streams/' . $obf_DQQXBDgiK1wTGyspDBYxMB83ODAxPxE;
			shell_exec($obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI);
		}

		if ($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_fingerprint_typ'] == 1) {
			if (($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_expire_date'] != NULL) || ($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_expire_date'] != '0') || ($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_expire_date'] != '')) {
				$days_left = date('Y-m-d', $obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_expire_date']);
				$show_text = 'You line will be expiring in | ' . $days_left;
			}
			else {
				$show_text = 'You line will be expiring | never';
			}

			$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg -y -i ' . DOCROOT . 'streams/' . $obf_DRE0DBocDCssJTMzK1wXNis7DAQQMDI . ' -vf drawbox="x=0:y=' . ($obf_DRYKj4OGg85KTA9EA0kDRUpLS08MgE - 25) . ':w=in_w:h=80:color=black@0.5:t=fill",drawtext="/home/xapicode/iptv_xapicode/fonts/Oswald-Bold.ttf:fontcolor=white:fontsize=' . $fontsize . ':text=' . $show_text . ':x=(w-tw)/2:y=' . $obf_DRYKj4OGg85KTA9EA0kDRUpLS08MgE . '" -c:v libx264 -b:v ' . $obf_DTMIPCgSAgUSKScHIgE5HAMOGRoyPwE[0]['stream_bitrate'] . 'k -preset ultrafast -c:a copy -muxdelay 0 ' . DOCROOT . 'streams/' . $obf_DQQXBDgiK1wTGyspDBYxMB83ODAxPxE;
			shell_exec($obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI);
		}

		if ($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_fingerprint_typ'] == 2) {
			$show_text = $obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_fingerprint_custom_text'];
			$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/quicksync/streaming/bin/ffmpeg -y -i ' . DOCROOT . 'streams/' . $obf_DRE0DBocDCssJTMzK1wXNis7DAQQMDI . ' -vf drawbox="x=0:y=' . ($obf_DRYKj4OGg85KTA9EA0kDRUpLS08MgE - 25) . ':w=in_w:h=80:color=black@0.5:t=fill",drawtext="/home/xapicode/iptv_xapicode/fonts/Oswald-Bold.ttf:fontcolor=white:fontsize=' . $fontsize . ':text=' . $show_text . ':x=(w-tw)/2:y=' . $obf_DRYKj4OGg85KTA9EA0kDRUpLS08MgE . '" -c:v libx264 -b:v ' . $obf_DTMIPCgSAgUSKScHIgE5HAMOGRoyPwE[0]['stream_bitrate'] . 'k -preset ultrafast -c:a copy -muxdelay 0 ' . DOCROOT . 'streams/' . $obf_DQQXBDgiK1wTGyspDBYxMB83ODAxPxE;
			shell_exec($obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI);
		}
	}
}

function stop_fingerprint($stream_id, $line_id, $server_id)
{
	global $db;
	$obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE = [$line_id];
	$obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI = $db->query('SELECT line_fingerprint FROM cms_lines WHERE line_id = ?', $obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE);
	$obf_DSckCQYTIxowPjcWPRYkDgELMycmKgE = json_decode($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_fingerprint'], true);
	$obf_DSckCQYTIxowPjcWPRYkDgELMycmKgE[0][SERVER] = 0;
	$obf_DRoKFSQOFx45BSQyHiYNCg1BBUiGRE = ['line_fingerprint_typ' => NULL, 'line_fingerprint_custom_text' => NULL, 'line_fingerprint_start_time' => 0, 'line_fingerprint' => json_encode($obf_DSckCQYTIxowPjcWPRYkDgELMycmKgE), 'line_fingerprint_target' => 0, 'line_fingerprint_stream_id' => 0, 'line_id' => $line_id];
	$obf_DTBcDzAaHT1cDRIzNB0JRkhOxAcNhE = $db->query("\n\t\t" . 'UPDATE cms_lines SET ' . "\n\t\t\t" . 'line_fingerprint_typ = :line_fingerprint_typ,' . "\n\t\t\t" . 'line_fingerprint_custom_text = :line_fingerprint_custom_text,' . "\n\t\t\t" . 'line_fingerprint_start_time = :line_fingerprint_start_time,' . "\n\t\t\t" . 'line_fingerprint = :line_fingerprint,' . "\n\t\t\t" . 'line_fingerprint_target = :line_fingerprint_target,' . "\n\t\t\t" . 'line_fingerprint_stream_id = :line_fingerprint_stream_id' . "\n\t\t" . 'WHERE line_id = :line_id', $obf_DRoKFSQOFx45BSQyHiYNCg1BBUiGRE);
}

function start_episode_download($episode_id)
{
	global $db;
	$obf_DSohKxoGKA0EKAkjAg5cJQcwKiE3EDI = [$episode_id];
	$obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE = $db->query('SELECT * FROM cms_serie_episodes WHERE episode_id = ?', $obf_DSohKxoGKA0EKAkjAg5cJQcwKiE3EDI);

	if (file_exists(DOCROOT . '/series/serie_finished/' . $episode_id . '.' . $obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_extension'])) {
		$obf_DTIlHgYyLionCAE4LgQtLh4bJjMGBhE = 'rm -rf ' . DOCROOT . 'series/serie_finished/' . $episode_id . '.' . $obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_extension'];
		shell_exec($obf_DTIlHgYyLionCAE4LgQtLh4bJjMGBhE);
	}

	$download = false;

	if ($obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_remote_source'] != '') {
		if ((parse_url($obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_remote_source'], PHP_URL_HOST) == 'youtube.com') || (parse_url($obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_remote_source'], PHP_URL_HOST) == 'https://www.youtube.com') || (parse_url($obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_remote_source'], PHP_URL_HOST) == 'www.youtube.com') || (parse_url($obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_remote_source'], PHP_URL_HOST) == 'mobil.youtube.com') || (parse_url($obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_remote_source'], PHP_URL_HOST) == 'youtu.be')) {
			$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = '/home/xapicode/iptv_xapicode/bin/youtube-dl -o "/home/xapicode/iptv_xapicode/series/serie_finished/' . $episode_id . '.' . $obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_extension'] . '" ' . $obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_remote_source'];
		}
		else {
			$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = 'wget ' . $obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_remote_source'] . ' -O ' . DOCROOT . 'series/serie_finished/' . $episode_id . '.' . $obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_extension'];
		}

		$obf_DSc3FUA5DDw2Hy4LJw8LESYiERIDNAE = shell_exec($obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI . ' > /dev/null 2>&1 & echo $!');

		if ($obf_DSc3FUA5DDw2Hy4LJw8LESYiERIDNAE != '') {
			$obf_DT8kKywvPjUGDA42GBIsPAkSOCIXBjI = ['episode_downloading_pid' => $obf_DSc3FUA5DDw2Hy4LJw8LESYiERIDNAE, 'episode_id' => $episode_id];
			$obf_DQUcGgsQLzYvEwEbEQQlMlwoPTYdJSI = $db->query("\n\t\t\t\t" . 'UPDATE cms_serie_episodes SET ' . "\n\t\t\t\t\t" . 'episode_downloading_pid = :episode_downloading_pid' . "\n\t\t\t\t" . 'WHERE episode_id = :episode_id', $obf_DT8kKywvPjUGDA42GBIsPAkSOCIXBjI);
			$download = true;
		}
		else {
			$download = false;
		}
	}
	else {
		$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = 'mv ' . $obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_local_source'] . ' ' . DOCROOT . 'series/serie_finished/' . $episode_id . '.' . $obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_extension'];
		$obf_DSc3FUA5DDw2Hy4LJw8LESYiERIDNAE = shell_exec($obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI . ' > /dev/null 2>&1 & echo $!');

		if ($obf_DSc3FUA5DDw2Hy4LJw8LESYiERIDNAE != '') {
			$obf_DT8kKywvPjUGDA42GBIsPAkSOCIXBjI = ['serie_episode_downloading_pid' => $obf_DSc3FUA5DDw2Hy4LJw8LESYiERIDNAE, 'episode_id' => $episode_id];
			$obf_DQUcGgsQLzYvEwEbEQQlMlwoPTYdJSI = $db->query("\n\t\t\t\t" . 'UPDATE cms_movies SET ' . "\n\t\t\t\t\t" . 'serie_episode_downloading_pid = :serie_episode_downloading_pid' . "\n\t\t\t\t" . 'WHERE episode_id = :episode_id', $obf_DT8kKywvPjUGDA42GBIsPAkSOCIXBjI);
			$download = true;
		}
		else {
			$download = false;
		}
	}

	return $download;
}

function start_episode_transcode($episode_id)
{
	global $db;
	$obf_DSohKxoGKA0EKAkjAg5cJQcwKiE3EDI = [$episode_id];
	$obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE = $db->query('SELECT * FROM cms_movies WHERE movie_id = ?', $obf_DSohKxoGKA0EKAkjAg5cJQcwKiE3EDI);
	$obf_DQIKDyIBBzkmMAMMXBkEKxkmLTESFAE = DOCROOT . 'series/serie_finished/' . $episode_id . '.' . $obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_extension'];
	$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 15s ' . DOCROOT . 'bin/ffprobe -i "' . $obf_DQIKDyIBBzkmMAMMXBkEKxkmLTESFAE . '" -v quiet -print_format json -show_streams 2>&1';
	$obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE = shell_exec($obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE);
	$obf_DQEXBzFbMT1cCQcxGBk7Ai0oJCkCRE = json_decode($obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE, true);
	$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = set_transcoding_profile($obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['episode_transcoding_id'], $obf_DQEXBzFbMT1cCQcxGBk7Ai0oJCkCRE['streams'][0]['width'], $obf_DQEXBzFbMT1cCQcxGBk7Ai0oJCkCRE['streams'][0]['height']);
	$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/ffmpeg -y -i ' . $obf_DQIKDyIBBzkmMAMMXBkEKxkmLTESFAE . ' ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' ' . DOCROOT . 'series/serie_finished/transcoding_' . $episode_id . '.' . $obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_extension'];
	$obf_DQM4CRwLFRUWFgEDDBEyOBIhKhUWMCI = shell_exec($obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI . ' > /dev/null 2>&1 & echo $!');
	$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = false;

	if ($obf_DQM4CRwLFRUWFgEDDBEyOBIhKhUWMCI != '') {
		$obf_DT8kKywvPjUGDA42GBIsPAkSOCIXBjI = ['episode_transcoding_pid' => $obf_DQM4CRwLFRUWFgEDDBEyOBIhKhUWMCI, 'episode_id' => $episode_id];
		$obf_DQUcGgsQLzYvEwEbEQQlMlwoPTYdJSI = $db->query("\n\t\t\t" . 'UPDATE cms_episode SET ' . "\n\t\t\t\t" . 'episode_transcode_pid = :episode_transcode_pid' . "\n\t\t\t" . 'WHERE episode_id = :episode_id', $obf_DT8kKywvPjUGDA42GBIsPAkSOCIXBjI);
		$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = true;
	}
	else {
		$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = false;
	}

	return $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI;
}

function check_allowed_bouquet_series($line_user, $serie_id)
{
	global $db;
	$obf_DT4WzQXFjE1LiYyOykwKjA9QAsIGzI = [];
	$obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE = [$line_user];
	$obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI = $db->query('SELECT line_bouquet_id FROM cms_lines WHERE line_user = ?', $obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE);
	$obf_DRUlBxcQKgQrPhcmDTQqBTIGBAozNQE = json_decode($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_bouquet_id'], true);

	foreach ($obf_DRUlBxcQKgQrPhcmDTQqBTIGBAozNQE as $obf_DQIBIyszMS4dXB85PiwmOzsaCjIiExE) {
		$obf_DSIXJRsHHQ4cKwkTBzQYJAYhFigaAyI = [$obf_DQIBIyszMS4dXB85PiwmOzsaCjIiExE];
		$obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE = $db->query('SELECT * FROM cms_bouquets WHERE bouquet_id = ?', $obf_DSIXJRsHHQ4cKwkTBzQYJAYhFigaAyI);

		if (0 < count($obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE)) {
			foreach ($obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE as $obf_DT8tGiMSIjwrASMNPScUCjY4Ew8DIQE) {
				foreach (array_filter($obf_DT8tGiMSIjwrASMNPScUCjY4Ew8DIQE) as $key => $value) {
					if ($key == 'bouquet_series') {
						$series = json_decode($value, true);

						foreach ($series as $obf_DQMPGwMsAwscOSw5BT8qDCE8PQkENzI) {
							$obf_DT4WzQXFjE1LiYyOykwKjA9QAsIGzI[] = $obf_DQMPGwMsAwscOSw5BT8qDCE8PQkENzI;
						}
					}
				}
			}
		}
	}

	if (in_array($serie_id, $obf_DT4WzQXFjE1LiYyOykwKjA9QAsIGzI)) {
		return true;
	}
	else {
		return false;
	}
}

function start_movie_download($movie_id)
{
	global $db;
	$obf_DTc8FgM0LCUPGQ8fJFwNAUAQLQgZCyI = [$movie_id];
	$obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI = $db->query('SELECT * FROM cms_movies WHERE movie_id = ?', $obf_DTc8FgM0LCUPGQ8fJFwNAUAQLQgZCyI);

	if (file_exists(DOCROOT . 'movie_finished/' . $movie_id . '.' . $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_extension'])) {
		$obf_DTIlHgYyLionCAE4LgQtLh4bJjMGBhE = 'rm -rf ' . DOCROOT . 'movie_finished/' . $movie_id . '.' . $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_extension'];
		shell_exec($obf_DTIlHgYyLionCAE4LgQtLh4bJjMGBhE);
	}

	$download = false;
	if (($obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_remote_source'] != '') && ($obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_local_source'] == '')) {
		$obf_DS4wMDkhIS8cKTYEHRA2DSMzMSUVGSI = str_replace(' ', '\\ ', $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_remote_source']);
		$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = 'wget ' . $obf_DS4wMDkhIS8cKTYEHRA2DSMzMSUVGSI . ' -O ' . DOCROOT . 'movies/movie_finished/' . $movie_id . '.' . $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_extension'];
		$obf_DQUjHCQ0CyomKyoQPTRcPzAiMgcTHhE = shell_exec($obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI . ' > /dev/null 2>&1 & echo $!');

		if ($obf_DQUjHCQ0CyomKyoQPTRcPzAiMgcTHhE != '') {
			$obf_DRIRKQ8PMSYDHy4jQBsqLQIxGC4NHxE = ['movie_downloading_pid' => $obf_DQUjHCQ0CyomKyoQPTRcPzAiMgcTHhE, 'movie_id' => $movie_id];
			$obf_DQYxFConOywOMARbXAkLDgY8MTkhCTI = $db->query("\n\t\t\t\t" . 'UPDATE cms_movies SET ' . "\n\t\t\t\t\t" . 'movie_downloading_pid = :movie_downloading_pid' . "\n\t\t\t\t" . 'WHERE movie_id = :movie_id', $obf_DRIRKQ8PMSYDHy4jQBsqLQIxGC4NHxE);
			$download = true;
		}
		else {
			$download = false;
		}
	}
	else {
		$obf_DS4wMDkhIS8cKTYEHRA2DSMzMSUVGSI = str_replace(' ', '\\ ', $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_local_source']);
		$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = 'mv ' . $obf_DS4wMDkhIS8cKTYEHRA2DSMzMSUVGSI . ' ' . DOCROOT . 'movies/movie_finished/' . $movie_id . '.' . $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_extension'];
		$obf_DQUjHCQ0CyomKyoQPTRcPzAiMgcTHhE = shell_exec($obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI . ' > /dev/null 2>&1 & echo $!');

		if ($obf_DQUjHCQ0CyomKyoQPTRcPzAiMgcTHhE != '') {
			$obf_DRIRKQ8PMSYDHy4jQBsqLQIxGC4NHxE = ['movie_downloading_pid' => $obf_DQUjHCQ0CyomKyoQPTRcPzAiMgcTHhE, 'movie_id' => $movie_id];
			$obf_DQYxFConOywOMARbXAkLDgY8MTkhCTI = $db->query("\n\t\t\t\t" . 'UPDATE cms_movies SET ' . "\n\t\t\t\t\t" . 'movie_downloading_pid = :movie_downloading_pid' . "\n\t\t\t\t" . 'WHERE movie_id = :movie_id', $obf_DRIRKQ8PMSYDHy4jQBsqLQIxGC4NHxE);
			$download = true;
		}
		else {
			$download = false;
		}
	}

	return $download;
}

function start_movie_transcode($movie_id)
{
	global $db;
	$obf_DTc8FgM0LCUPGQ8fJFwNAUAQLQgZCyI = [$movie_id];
	$obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI = $db->query('SELECT * FROM cms_movies WHERE movie_id = ?', $obf_DTc8FgM0LCUPGQ8fJFwNAUAQLQgZCyI);
	$obf_DQQcNhIYKh5AOxIPQcLBioTLR85EAE = DOCROOT . 'movies/movie_finished/' . $movie_id . '.' . $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_extension'];
	$obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE = '/usr/bin/timeout 15s ' . DOCROOT . 'bin/ffprobe -i "' . $obf_DQQcNhIYKh5AOxIPQcLBioTLR85EAE . '" -v quiet -print_format json -show_streams 2>&1';
	$obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE = shell_exec($obf_DRkYJSwKkA9XBAwOAUxXBwGMygGCgE);
	$obf_DQEXBzFbMT1cCQcxGBk7Ai0oJCkCRE = json_decode($obf_DR4MIigMEwkSJjgKzQQKj0uLRwwAQE, true);
	$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = set_transcoding_profile($obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_transcode_id'], $obf_DQEXBzFbMT1cCQcxGBk7Ai0oJCkCRE['streams'][0]['width'], $obf_DQEXBzFbMT1cCQcxGBk7Ai0oJCkCRE['streams'][0]['height']);
	$obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI = DOCROOT . 'bin/ffmpeg -y -i ' . $obf_DQQcNhIYKh5AOxIPQcLBioTLR85EAE . ' ' . $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI . ' ' . DOCROOT . 'movies/movie_finished/transcoding_' . $movie_id . '.' . $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_extension'];
	$obf_DQQvGz44JlwKJAwIBwsSEDwaGh8oFQE = shell_exec($obf_DSQnCDczAgEVNg0IPwkBNhUeFQ00PyI . ' > /dev/null 2>&1 & echo $!');
	$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = false;

	if ($obf_DQQvGz44JlwKJAwIBwsSEDwaGh8oFQE != '') {
		$obf_DRIRKQ8PMSYDHy4jQBsqLQIxGC4NHxE = ['movie_transcoding_pid' => $obf_DQQvGz44JlwKJAwIBwsSEDwaGh8oFQE, 'movie_id' => $movie_id];
		$obf_DQYxFConOywOMARbXAkLDgY8MTkhCTI = $db->query("\n\t\t\t" . 'UPDATE cms_movies SET ' . "\n\t\t\t\t" . 'movie_transcoding_pid = :movie_transcoding_pid' . "\n\t\t\t" . 'WHERE movie_id = :movie_id', $obf_DRIRKQ8PMSYDHy4jQBsqLQIxGC4NHxE);
		$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = true;
	}
	else {
		$obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI = false;
	}

	return $obf_DUA2CgYGPQcBBwkZAyIBDjcDDgtcHSI;
}

function check_allowed_bouquet_movie($line_user, $movie_id)
{
	global $db;
	$obf_DSYSMicRFhQwOD8KNx8NxgVDggaNQE = [];
	$obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE = [$line_user];
	$obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI = $db->query('SELECT line_bouquet_id FROM cms_lines WHERE line_user = ?', $obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE);
	$obf_DRUlBxcQKgQrPhcmDTQqBTIGBAozNQE = json_decode($obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_bouquet_id'], true);

	foreach ($obf_DRUlBxcQKgQrPhcmDTQqBTIGBAozNQE as $obf_DQIBIyszMS4dXB85PiwmOzsaCjIiExE) {
		$obf_DSIXJRsHHQ4cKwkTBzQYJAYhFigaAyI = [$obf_DQIBIyszMS4dXB85PiwmOzsaCjIiExE];
		$obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE = $db->query('SELECT * FROM cms_bouquets WHERE bouquet_id = ?', $obf_DSIXJRsHHQ4cKwkTBzQYJAYhFigaAyI);

		if (0 < count($obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE)) {
			foreach ($obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE as $obf_DT8tGiMSIjwrASMNPScUCjY4Ew8DIQE) {
				foreach (array_filter($obf_DT8tGiMSIjwrASMNPScUCjY4Ew8DIQE) as $key => $value) {
					if ($key == 'bouquet_movies') {
						$movies = json_decode($value, true);

						foreach ($movies as $obf_DTMnEiwFPRMfMRMXDAItDwsoFxM0KyI) {
							$obf_DSYSMicRFhQwOD8KNx8NxgVDggaNQE[] = $obf_DTMnEiwFPRMfMRMXDAItDwsoFxM0KyI;
						}
					}
				}
			}
		}
	}

	if (in_array($movie_id, $obf_DSYSMicRFhQwOD8KNx8NxgVDggaNQE)) {
		return true;
	}
	else {
		return false;
	}
}

function shuffle_server($stream_server_array)
{
	global $db;
	$obf_DQIqER0BNRUdFVw1DSQNET8pDyoKCI = [];
	$obf_DSkoBQg0MBg9PxEBHCI5GjAWBhw2IjI = [];
	$obf_DQITPBMEHxI8IwFbJiYROS8tDAwSOCI = [];
	$obf_DTMuCCgJGQMrKkA1KQQhMTI3AQs9NSI = $db->query('SELECT setting_lb_limit FROM cms_settings');

	foreach ($stream_server_array as $obf_DRVAKyMIHgoqBwwrKQUfWx0rCR0TCjI) {
		$obf_DUAzPCIDCxU0NRkCQDwcMxcpFhcWLAE = [$obf_DRVAKyMIHgoqBwwrKQUfWx0rCR0TCjI];
		$obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI = $db->query('SELECT server_id, server_bandwidth_limit, server_up_speed, server_client_limit FROM cms_server WHERE server_id = ?', $obf_DUAzPCIDCxU0NRkCQDwcMxcpFhcWLAE);

		if ($obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_bandwidth_limit'] != NULL) {
			$obf_DT4THyU4QDwYEi0LJBE3CS8UPxIVCRE = explode(' ', trim($obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_up_speed']))[0];

			if ($obf_DT4THyU4QDwYEi0LJBE3CS8UPxIVCRE < $obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_bandwidth_limit']) {
				$obf_DQIqER0BNRUdFVw1DSQNET8pDyoKCI[] = $obf_DRVAKyMIHgoqBwwrKQUfWx0rCR0TCjI;
			}
		}
		else {
			$obf_DT4THyU4QDwYEi0LJBE3CS8UPxIVCRE = explode(' ', trim($obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_up_speed']))[0];

			if ($obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_bandwidth_limit'] < $obf_DT4THyU4QDwYEi0LJBE3CS8UPxIVCRE) {
				$obf_DQIqER0BNRUdFVw1DSQNET8pDyoKCI[] = $obf_DRVAKyMIHgoqBwwrKQUfWx0rCR0TCjI;
			}
			else {
				$obf_DQITPBMEHxI8IwFbJiYROS8tDAwSOCI[] = $obf_DRVAKyMIHgoqBwwrKQUfWx0rCR0TCjI;
			}
		}

		if ($obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_client_limit'] != 0) {
			$obf_DT08G1wnBwMpKycPI1sYExIfXD8hJgE = get_server_activity($obf_DRVAKyMIHgoqBwwrKQUfWx0rCR0TCjI, false);

			if ($obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_client_limit'] < $obf_DT08G1wnBwMpKycPI1sYExIfXD8hJgE) {
				$obf_DSkoBQg0MBg9PxEBHCI5GjAWBhw2IjI[] = $obf_DRVAKyMIHgoqBwwrKQUfWx0rCR0TCjI;
			}
		}
		else {
			$obf_DQITPBMEHxI8IwFbJiYROS8tDAwSOCI[] = $obf_DRVAKyMIHgoqBwwrKQUfWx0rCR0TCjI;
		}
	}

	if ($obf_DTMuCCgJGQMrKkA1KQQhMTI3AQs9NSI[0]['setting_lb_limit'] == 1) {
		shuffle($obf_DQIqER0BNRUdFVw1DSQNET8pDyoKCI);
		return $obf_DQIqER0BNRUdFVw1DSQNET8pDyoKCI[0];
	}
	else if ($obf_DTMuCCgJGQMrKkA1KQQhMTI3AQs9NSI[0]['setting_lb_limit'] == 2) {
		shuffle($obf_DSkoBQg0MBg9PxEBHCI5GjAWBhw2IjI);
		return $obf_DSkoBQg0MBg9PxEBHCI5GjAWBhw2IjI[0];
	}
	else {
		shuffle($obf_DQITPBMEHxI8IwFbJiYROS8tDAwSOCI);
		return $obf_DQITPBMEHxI8IwFbJiYROS8tDAwSOCI[0];
	}
}

function check_allowed_ip($line_user, $line_allowed_ip)
{
	global $db;

	if ($line_allowed_ip != '') {
		$allowed_ips = json_decode($line_allowed_ip, true);

		if (in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
			$return = true;
		}
		else {
			$return = false;
		}
	}
	else {
		$return = true;
	}

	return $return;
}

function check_allowed_ua($line_user, $allowed_ua, $user_agent)
{
	global $db;

	if ($user_agent != '') {
		if ($allowed_ua != '') {
			$obf_DTA7JDQVFAoxDgEqDggBPRw4MhABxE = [];
			$allowed_ua = json_decode($allowed_ua, true);

			foreach ($allowed_ua as $ua) {
				if (preg_match_all('/(.*)' . strtolower($ua) . '(.*)/', strtolower($user_agent), $matches)) {
					if (count($matches)) {
						$obf_DTA7JDQVFAoxDgEqDggBPRw4MhABxE[] = $user_agent;
					}
				}
			}

			if (in_array($user_agent, $obf_DTA7JDQVFAoxDgEqDggBPRw4MhABxE)) {
				$return = true;
			}
			else {
				$return = false;
			}
		}
		else {
			$return = true;
		}
	}

	return $return;
}

function check_allowed_isp($line_user, $allowed_isp)
{
	global $db;

	if ($allowed_isp != '') {
		$ip = $_SERVER['REMOTE_ADDR'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://rdap.db.ripe.net/ip/' . $ip);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
		$returnValue = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($returnValue, true);
		$obf_DQ49Oz8aGhYyKhIYFigyHScSLSkxMBE = strtolower($result['remarks'][0]['description'][0]);
		$allowed_isp = json_decode($allowed_isp, true);
		$obf_DQEsQDgMCVsqCR4mODIsHjEbDTMKChE = [];

		foreach ($allowed_isp as $isp) {
			if (preg_match_all('/(.*)' . strtolower($isp) . '(.*)/', strtolower($obf_DQ49Oz8aGhYyKhIYFigyHScSLSkxMBE), $matches)) {
				if (count($matches)) {
					$obf_DQEsQDgMCVsqCR4mODIsHjEbDTMKChE[] = $obf_DQ49Oz8aGhYyKhIYFigyHScSLSkxMBE;
				}
			}
		}

		if (in_array($obf_DQ49Oz8aGhYyKhIYFigyHScSLSkxMBE, $obf_DQEsQDgMCVsqCR4mODIsHjEbDTMKChE)) {
			$return = true;
		}
		else {
			$return = false;
		}
	}
	else {
		$return = true;
	}

	return $return;
}

function check_allowed_bouquet_stream($line_user, $line_bouquet_id, $stream_id)
{
	global $db;
	$obf_DQwmHwIxBR8eBC04Ph0tLi0JJBoaPxE = [];
	$obf_DQsxGhY0FhE8OyEZIwMLKhoyAT8RGQE = [];
	$obf_DRUlBxcQKgQrPhcmDTQqBTIGBAozNQE = json_decode($line_bouquet_id, true);

	foreach ($obf_DRUlBxcQKgQrPhcmDTQqBTIGBAozNQE as $obf_DQIBIyszMS4dXB85PiwmOzsaCjIiExE) {
		$obf_DQsxGhY0FhE8OyEZIwMLKhoyAT8RGQE[] = $obf_DQIBIyszMS4dXB85PiwmOzsaCjIiExE;
	}

	$obf_DQsxGhY0FhE8OyEZIwMLKhoyAT8RGQE = implode(',', $obf_DQsxGhY0FhE8OyEZIwMLKhoyAT8RGQE);
	$obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE = $db->query('SELECT bouquet_streams FROM cms_bouquets WHERE bouquet_id IN (' . $obf_DQsxGhY0FhE8OyEZIwMLKhoyAT8RGQE . ')');

	if (0 < count($obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE)) {
		foreach ($obf_DRooByoNJyVALDUJJSEpMAMSKy8mAgE as $obf_DT8tGiMSIjwrASMNPScUCjY4Ew8DIQE) {
			$obf_DRECOCwJKhk3MRcQLDEOWxIxFhQEHgE = json_decode($obf_DT8tGiMSIjwrASMNPScUCjY4Ew8DIQE['bouquet_streams'], true);
			if ($obf_DRECOCwJKhk3MRcQLDEOWxIxFhQEHgE && ((gettype($obf_DRECOCwJKhk3MRcQLDEOWxIxFhQEHgE) == 'array') || (gettype($obf_DRECOCwJKhk3MRcQLDEOWxIxFhQEHgE) == 'object'))) {
				foreach ($obf_DRECOCwJKhk3MRcQLDEOWxIxFhQEHgE as $value) {
					$obf_DQwmHwIxBR8eBC04Ph0tLi0JJBoaPxE[] = $value;
				}
			}
		}
	}

	if (in_array($stream_id, $obf_DQwmHwIxBR8eBC04Ph0tLi0JJBoaPxE)) {
		return true;
	}
	else {
		return false;
	}
}

function check_reshare_dedection()
{
	global $db;
	$obf_DS4DHgkuBgsxMwE4JDwNBRAoHhwyERE = [1];
	$obf_DQ0zFRwMNjclNVwLFDUMCRgRQBQQDzI = $db->query('SELECT setting_reshare_protection FROM cms_settings WHERE setting_reshare_protection = ?', $obf_DS4DHgkuBgsxMwE4JDwNBRAoHhwyERE);

	if (0 < count($obf_DQ0zFRwMNjclNVwLFDUMCRgRQBQQDzI)) {
		return true;
	}
	else {
		return false;
	}
}

function check_flood_dedection()
{
	global $db;
	$obf_DSsQLg47KiMQHyceCiorFhYRNiQiORE = [1];
	$obf_DTsKDwMfBkAvFB4TLC8NARIBIwchIxE = $db->query('SELECT setting_flood_protection FROM cms_settings WHERE setting_flood_protection = ?', $obf_DSsQLg47KiMQHyceCiorFhYRNiQiORE);

	if (0 < count($obf_DTsKDwMfBkAvFB4TLC8NARIBIwchIxE)) {
		return true;
	}
	else {
		return false;
	}
}

function check_line_user($line_user)
{
	global $db;
	$obf_DT4wMQkkHRMaMSEmFRQ2GDMnGgMXDI = [$line_user];
	$obf_DSoQXBMTNyUCNSIQPjAUJBkFj84GxE = $db->query('SELECT line_id FROM cms_lines WHERE line_user = ?', $obf_DT4wMQkkHRMaMSEmFRQ2GDMnGgMXDI);

	if (0 < count($obf_DSoQXBMTNyUCNSIQPjAUJBkFj84GxE)) {
		return true;
	}
	else {
		return false;
	}
}

function check_line_is_expired($line_user)
{
	global $db;
	$obf_DT4wMQkkHRMaMSEmFRQ2GDMnGgMXDI = [$line_user, 2];
	$obf_DSoQXBMTNyUCNSIQPjAUJBkFj84GxE = $db->query('SELECT line_id FROM cms_lines WHERE line_user = ? AND line_status = ?', $obf_DT4wMQkkHRMaMSEmFRQ2GDMnGgMXDI);

	if (0 < count($obf_DSoQXBMTNyUCNSIQPjAUJBkFj84GxE)) {
		return true;
	}
	else {
		return false;
	}
}

function check_security_token($token)
{
	global $db;
	$obf_DT8HNQkcNQI5HgomHyw8OwovHhgQNjI = [$token];
	$obf_DSYYMD4VPxYpWxwtMRZbMDsmFxoqJhE = $db->query('SELECT setting_id FROM cms_settings WHERE setting_security_token = ?', $obf_DT8HNQkcNQI5HgomHyw8OwovHhgQNjI);

	if (0 < count($obf_DSYYMD4VPxYpWxwtMRZbMDsmFxoqJhE)) {
		return true;
	}
	else {
		return false;
	}
}

function insert_into_loglist($remote_ip = '', $user_agent = '', $query_string = '')
{
	global $db;
	$obf_DQMHMgocPi8OFR4ZDTYlNSYpHhEYKiI = ['log_ip' => $remote_ip, 'log_ua' => $user_agent, 'log_query' => $query_string, 'log_time' => time(), 'log_server' => SERVER, 'log_proxy' => 0];
	$insert_log = $db->query('INSERT INTO cms_log (log_ip, log_ua, log_query, log_time, log_server, log_proxy) VALUES (:log_ip, :log_ua, :log_query, :log_time, :log_server, :log_proxy)', $obf_DQMHMgocPi8OFR4ZDTYlNSYpHhEYKiI);
}

function insert_into_bannlist($line_user = '', $remote_ip = '', $bann_title, $bann_note)
{
	global $db;
	$obf_DR0cGgIHOC8dBgQjLhoXFjIwKRssFiI = ['bann_time' => time(), 'bann_ip' => $remote_ip, 'bann_line_id' => $line_user, 'bann_server' => SERVER, 'bann_note' => '<strong>' . $bann_title . ': </strong> ' . $bann_note];
	$obf_DQwUGRIECRkWExsJFS4dJSsAz0sWwE = $db->query('INSERT INTO cms_bannlist (bann_time, bann_ip, bann_line_id, bann_server, bann_note) VALUES (:bann_time, :bann_ip, :bann_line_id, :bann_server, :bann_note)', $obf_DR0cGgIHOC8dBgQjLhoXFjIwKRssFiI);
}

function get_line_id_by_name($line_user)
{
	global $db;
	$obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE = [$line_user];
	$obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI = $db->query('SELECT line_id FROM cms_lines WHERE line_user = ?', $obf_DSsbEDYLMy45NCo3IjwxGiIYBg81PBE);
	return $obf_DTc3XA8MzsHTwbDjkMNDUVMBocBiI[0]['line_id'];
}

function get_broadcast_port($server_id)
{
	global $db;
	$obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI = [$server_id];
	$obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI = $db->query('SELECT server_broadcast_port FROM cms_server WHERE server_id = ?', $obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI);
	return $obf_DQgxCyxbNBgUJio5Jw8VXCQUPAYBBiI[0]['server_broadcast_port'];
}

function iptables_add($remote_ip)
{
	shell_exec('sudo /sbin/iptables -A INPUT -s ' . $remote_ip . ' -j DROP');
}

function stream_segments($stream_id)
{
	if (file_exists(DOCROOT . 'streams/' . $stream_id . '_.m3u8')) {
		$obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE = file_get_contents(DOCROOT . 'streams/' . $stream_id . '_.m3u8');
		$obf_DTQRGBMvDgEOCyZcBxkJOD0fDRQJBgE = explode(',', $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE);
		$segment_array = [];

		foreach (preg_split('/((' . "\r" . '?' . "\n" . ')|(' . "\r\n" . '?))/', $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) as $line) {
			if (strpos($line, '.ts') !== false) {
				preg_match('%([^`]*?)\\?%', $line, $obf_DVw4KAUuJgctOT05OCEtFCEZIiIYFQE);
				array_push($segment_array, $line);
			}
		}

		return $segment_array;
	}
	else {
		return false;
	}
}

function stream_segment_of_adaptive($stream_id, $adaptive_profile)
{
	$obf_DTUYGQoqHhwWEys9MwYOLQ0iJCUwFxE = [];
	$segment_array = [];
	$profile = json_decode($adaptive_profile, true);

	foreach ($profile as $key => $value) {
		$obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE = file_get_contents(DOCROOT . 'streams/' . $stream_id . '' . $key . '_.m3u8');

		foreach (preg_split('/((' . "\r" . '?' . "\n" . ')|(' . "\r\n" . '?))/', $obf_DRoKCz4hEww4H1s2GzczFA8oIxYiCAE) as $line) {
			if (strpos($line, '.ts') !== false) {
				preg_match('%([^`]*?)\\?%', $line, $obf_DVw4KAUuJgctOT05OCEtFCEZIiIYFQE);
				$segment_array[$key] = $line;
			}
		}
	}

	return json_encode($segment_array);
}

function segment_playlist($playlist, $prebuffer = 0)
{
	if (file_exists($playlist)) {
		$source = file_get_contents($playlist);

		if (preg_match_all('/(.*?).ts/', $source, $matches)) {
			if (0 < $prebuffer) {
				$obf_DR4mNQMMGQMeEiMcPQQOC48QDgQDQE = intval($prebuffer / 10);
				return array_slice($matches[0], -1 * $obf_DR4mNQMMGQMeEiMcPQQOC48QDgQDQE);
			}
			else {
				return [array_pop($matches[0])];
			}
		}
	}

	return false;
}

function segment_buffer()
{
	global $db;
	$obf_DTMuCCgJGQMrKkA1KQQhMTI3AQs9NSI = $db->query('SELECT setting_prebuffer_sec, setting_buffersize_reading FROM cms_settings');
	return $obf_DTMuCCgJGQMrKkA1KQQhMTI3AQs9NSI[0]['setting_prebuffer_sec'];
}

function check_line_connection_hls($line_id, $stream_id)
{
	global $db;
	$obf_DTIRQAY7DjwyWwkXDwUGCBMTBCIXGBE = [$line_id, 'hls'];
	$obf_DSwIOy0GDxoeFScvNCI3MjU4OTwNLBE = $db->query('SELECT stream_activity_id, stream_activity_stream_id FROM cms_stream_activity WHERE stream_activity_line_id = ? AND stream_activity_typ = ?', $obf_DTIRQAY7DjwyWwkXDwUGCBMTBCIXGBE);

	if (0 < count($obf_DSwIOy0GDxoeFScvNCI3MjU4OTwNLBE)) {
		if ($obf_DSwIOy0GDxoeFScvNCI3MjU4OTwNLBE[0]['stream_activity_stream_id'] == $stream_id) {
			return true;
		}
		else {
			exit('unable to connection. reason: max allowed channels on hls is reached!');
		}
	}
	else {
		return true;
	}
}

function show_all_series_on_bouquet()
{
	global $db;
	$obf_DQ8pIhUeIjgXFRs2PjU2ExsoPT4GTI = [1];
	$obf_DTMuCCgJGQMrKkA1KQQhMTI3AQs9NSI = $db->query('SELECT setting_id FROM cms_settings WHERE setting_show_all_episodes = ?', $obf_DQ8pIhUeIjgXFRs2PjU2ExsoPT4GTI);

	if (0 < count($obf_DTMuCCgJGQMrKkA1KQQhMTI3AQs9NSI)) {
		return true;
	}
	else {
		return false;
	}
}

function get_movie_by_id($movie_id)
{
	global $db;
	$obf_DTc8FgM0LCUPGQ8fJFwNAUAQLQgZCyI = [$movie_id];
	$obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI = $db->query('SELECT movie_name FROM cms_movies WHERE movie_id = ?', $obf_DTc8FgM0LCUPGQ8fJFwNAUAQLQgZCyI);
	return $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_name'];
}

function get_movie_extension($movie_id)
{
	global $db;
	$obf_DTc8FgM0LCUPGQ8fJFwNAUAQLQgZCyI = [$movie_id];
	$obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI = $db->query('SELECT movie_extension FROM cms_movies WHERE movie_id = ?', $obf_DTc8FgM0LCUPGQ8fJFwNAUAQLQgZCyI);
	return $obf_DSo8JxY3OSMjFgoDAigECRQdEiwaJjI[0]['movie_extension'];
}

function get_episode_extension($episode_id)
{
	global $db;
	$obf_DSohKxoGKA0EKAkjAg5cJQcwKiE3EDI = [$episode_id];
	$obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE = $db->query('SELECT serie_episode_extension FROM cms_serie_episodes WHERE episode_id = ?', $obf_DSohKxoGKA0EKAkjAg5cJQcwKiE3EDI);
	return $obf_DSw3CzEBEjMyEgc9OCYcORw0QBFbFxE[0]['serie_episode_extension'];
}

function season_number($season_number)
{
	if ($season_number < 10) {
		$season_number = '0' . $season_number;
	}
	else {
		$season_number = $season_number;
	}

	return $season_number;
}

function episode_number($episode_number)
{
	if ($episode_number < 10) {
		$episode_number = '0' . $episode_number;
	}
	else {
		$episode_number = $episode_number;
	}

	return $episode_number;
}

function get_from_cookie($cookie, $type)
{
	if (!empty($cookie)) {
		$explode = explode(';', $cookie);

		foreach ($explode as $data) {
			$data = explode('=', $data);
			$output[trim($data[0])] = trim($data[1]);
		}

		switch ($type) {
		case 'mac':
			if (array_key_exists('mac', $output)) {
				return base64_encode(strtoupper(urldecode($output['mac'])));
			}
		}
	}

	return false;
}

function prepair_mag_cols($array)
{
	$output = [];

	foreach ($array as $key => $value) {
		if (($key == 'mac') || ($key == 'ver') || ($key == 'hw_version')) {
			$output[$key] = base64_decode($value);
		}

		$output[$key] = $value;
	}

	unset($output['fav_channels']);
	return $output;
}
