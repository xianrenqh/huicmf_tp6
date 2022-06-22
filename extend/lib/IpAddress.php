<?php
/**
 * Created by PhpStorm.
 * User: 投实科技
 * Date: 2021-12-10
 * Time: 9:24:59
 * Info: 根据ip解析为地区-【省市】
 * Desc: 1、优先使用纯真ip库；2、1失败使用太平洋ip库解析；3、2失败使用百度ip库解析
 */

namespace lib;

class IpAddress
{

    /**
     * ip地址转换为地区名
     */
    public function ipToAddress($clientIP = '')
    {
        //优先使用纯真ip库解析
        $address               = $this->convertIp($clientIP);
        $returnAddress         = $address;
        $returnAddress['type'] = 'ct';
        if (empty($returnAddress['province']) || empty($returnAddress['city'])) {
            //使用太平洋ip库解析
            $returnAddress         = $this->modifyAddressPconline($clientIP);
            $returnAddress['type'] = 'pconline';
            if (empty($returnAddress['province']) || empty($returnAddress['city'])) {
                //如果太平洋解析失败，使用百度ip库解析
                $returnAddress         = $this->modifyAddressBaidu($clientIP);
                $returnAddress['type'] = 'baidu';
            } else {
                return $returnAddress;
            }
        }

        return $returnAddress;
    }

    /**
     * 根据IP获取请求地区（太平洋IP库）
     *
     * @param $ip
     *
     * @return 所在位置
     */
    private function modifyAddressPconline($ip)
    {
        $result = ['province' => '', 'city' => ''];
        sleep(6);// 10次并发
        $content = @file_get_contents('http://whois.pconline.com.cn/ipJson.jsp?ip='.$ip.'&json=true');
        $content = iconv('GB2312', 'UTF-8', $content);
        $arr     = json_decode($content, true);
        if (is_array($arr) && $arr['regionCode'] == 0) {
            $result['province'] = $arr['pro'];
            $result['city']     = $arr['city'];
        }

        return $result;

    }

    /**
     * 使用百度接口api获取地区
     * 返回省市的数组
     *
     * @param $ip
     *
     * @return false|string[]
     */
    private function modifyAddressBaidu($ip)
    {
        $result = ['province' => '', 'city' => ''];
        $ak     = 'vtuYRSFe2CZtN9KzlWnGnTOpZONb0bDW';
        sleep(6);// 10次并发
        $url         = 'http://api.map.baidu.com/location/ip?ak='.$ak.'&ip='.$ip.'&coor=bd09ll'; //HTTP协议
        $curl_result = cmf_curl_get($url);
        $return      = json_decode($curl_result, true);
        if ( ! is_array($return) || ! isset($return['status']) || $return['status'] != 0) {
            return $result;
        }
        $result['province'] = $return['content']['address_detail']['province'];
        $result['city']     = $return['content']['address_detail']['city'];

        return $result;

    }

    /**
     * 分割纯真ip库解析出来的地址
     *
     * @param $address
     */
    private function formatAddress($address)
    {
        $result = ['province' => '', 'city' => ''];

        if (empty($address)) {
            return $result;
        }
        if (preg_match("/(省)/", $address, $match)) {
            if (empty($match)) {
                $result['province'] = $address;

                return $result;
            }
            $address_arr        = explode($match[0], $address);
            $city               = explode(" ", $address_arr[1]);
            $result['province'] = $address_arr[0].'省';
            $result['city']     = $city[0];
        } elseif (preg_match("/(自治区)/", $address, $match)) {
            if (empty($match)) {
                $result['province'] = $address;

                return $result;
            }
            $address_arr        = explode($match[0], $address);
            $city               = explode(" ", $address_arr[1]);
            $result['province'] = $address_arr[0].'自治区';
            $result['city']     = $city[0];
        } elseif (preg_match("/市/", $address, $match)) {
            if (empty($match)) {
                $result['province'] = $address;

                return $result;
            }
            $address_arr        = explode($match[0], $address);
            $result['province'] = $address_arr[0].'市';
            $result['city']     = $address_arr[0].'市';
        } elseif ($address != '中国') {
            $arr = ['广西', '内蒙古', '宁夏', '西藏', '新疆'];
            foreach ($arr as $name) {
                if ($name == $address) {
                    $result['province'] = $name;

                    return $result;
                }
                if (mb_strpos($address, $name) === false) {
                    continue;
                }

                $result['city']     = str_replace($name, '', $address);
                $result['province'] = $name;
            }
        } else {
            $result['province'] = $address;
        }

        return $result;
    }

    /**
     * 纯真数据库调用函数（需要下载纯真数据库文件）
     *
     * @param $ip
     *
     * @return false|string
     */
    private function convertIp($ip)
    {
        $ip1num   = 0;
        $ip2num   = 0;
        $ipAddr1  = "";
        $ipAddr2  = "";
        $dat_path = './tpl/qqwry.dat';        //纯真数据库文件位置
        if ( ! preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $ip)) {
            return ['msg' => 'IP Address Error', 'province' => '', 'city' => ''];
        }
        if ( ! $fd = @fopen($dat_path, 'rb')) {
            return ['msg' => 'IP date file not exists or access denied', 'province' => '', 'city' => ''];
        }
        $ip        = explode('.', $ip);
        $ipNum     = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];
        $DataBegin = fread($fd, 4);
        $DataEnd   = fread($fd, 4);
        $ipbegin   = implode('', unpack('L', $DataBegin));
        if ($ipbegin < 0) {
            $ipbegin += pow(2, 32);
        }
        $ipend = implode('', unpack('L', $DataEnd));
        if ($ipend < 0) {
            $ipend += pow(2, 32);
        }
        $ipAllNum = ($ipend - $ipbegin) / 7 + 1;
        $BeginNum = 0;
        $EndNum   = $ipAllNum;
        while ($ip1num > $ipNum || $ip2num < $ipNum) {
            $Middle = intval(($EndNum + $BeginNum) / 2);
            fseek($fd, $ipbegin + 7 * $Middle);
            $ipData1 = fread($fd, 4);
            if (strlen($ipData1) < 4) {
                fclose($fd);

                return ['msg' => 'System Error', 'province' => '', 'city' => ''];
            }
            $ip1num = implode('', unpack('L', $ipData1));
            if ($ip1num < 0) {
                $ip1num += pow(2, 32);
            }

            if ($ip1num > $ipNum) {
                $EndNum = $Middle;
                continue;
            }
            $DataSeek = fread($fd, 3);
            if (strlen($DataSeek) < 3) {
                fclose($fd);

                return ['msg' => 'System Error', 'province' => '', 'city' => ''];
            }
            $DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
            fseek($fd, $DataSeek);
            $ipData2 = fread($fd, 4);
            if (strlen($ipData2) < 4) {
                fclose($fd);

                return ['msg' => 'System Error', 'province' => '', 'city' => ''];
            }
            $ip2num = implode('', unpack('L', $ipData2));
            if ($ip2num < 0) {
                $ip2num += pow(2, 32);
            }
            if ($ip2num < $ipNum) {
                if ($Middle == $BeginNum) {
                    fclose($fd);

                    return ['msg' => 'Unknown', 'province' => '', 'city' => ''];
                }
                $BeginNum = $Middle;
            }
        }
        $ipFlag = fread($fd, 1);
        if ($ipFlag == chr(1)) {
            $ipSeek = fread($fd, 3);
            if (strlen($ipSeek) < 3) {
                fclose($fd);

                return ['msg' => 'Unknown', 'province' => '', 'city' => ''];
            }
            $ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
            fseek($fd, $ipSeek);
            $ipFlag = fread($fd, 1);
        }
        if ($ipFlag == chr(2)) {
            $AddrSeek = fread($fd, 3);
            if (strlen($AddrSeek) < 3) {
                fclose($fd);

                return ['msg' => 'Unknown', 'province' => '', 'city' => ''];
            }
            $ipFlag = fread($fd, 1);
            if ($ipFlag == chr(2)) {
                $AddrSeek2 = fread($fd, 3);
                if (strlen($AddrSeek2) < 3) {
                    fclose($fd);

                    return ['msg' => 'Unknown', 'province' => '', 'city' => ''];
                }
                $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
                fseek($fd, $AddrSeek2);
            } else {
                fseek($fd, -1, SEEK_CUR);
            }
            while (($char = fread($fd, 1)) != chr(0)) {
                $ipAddr2 .= $char;
            }
            $AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
            fseek($fd, $AddrSeek);
            while (($char = fread($fd, 1)) != chr(0)) {
                $ipAddr1 .= $char;
            }
        } else {
            fseek($fd, -1, SEEK_CUR);
            while (($char = fread($fd, 1)) != chr(0)) {
                $ipAddr1 .= $char;
            }
            $ipFlag = fread($fd, 1);
            if ($ipFlag == chr(2)) {
                $AddrSeek2 = fread($fd, 3);
                if (strlen($AddrSeek2) < 3) {
                    fclose($fd);

                    return ['msg' => 'Unknown', 'province' => '', 'city' => ''];
                }
                $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
                fseek($fd, $AddrSeek2);
            } else {
                fseek($fd, -1, SEEK_CUR);
            }
            while (($char = fread($fd, 1)) != chr(0)) {
                $ipAddr2 .= $char;
            }
        }
        fclose($fd);
        if (preg_match('/http/i', $ipAddr2)) {
            $ipAddr2 = '';
        }
        $ipaddr = "$ipAddr1 $ipAddr2";

        $ipaddr = preg_replace('/CZ88.NET/is', '', $ipaddr);
        $ipaddr = preg_replace('/^s*/is', '', $ipaddr);
        $ipaddr = preg_replace('/s*$/is', '', $ipaddr);
        if (preg_match('/http/i', $ipaddr) || $ipaddr == '') {
            $ipaddr = 'Unknown';
        }
        //编码转换
        $ipaddr = iconv('gbk', 'utf-8//IGNORE', $ipaddr);
        $return = $this->formatAddress($ipaddr);

        return $return;
    }

}
