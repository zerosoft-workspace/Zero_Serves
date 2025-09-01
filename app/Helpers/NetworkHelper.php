<?php

namespace App\Helpers;

class NetworkHelper
{
    /**
     * Get the appropriate host for QR code generation
     * If localhost/127.0.0.1, returns LAN IP
     * Otherwise returns the current host
     */
    public static function getQrHost()
    {
        $currentHost = request()->getHost();
        
        // If it's localhost or 127.0.0.1, get LAN IP
        if (in_array($currentHost, ['localhost', '127.0.0.1', '::1'])) {
            return self::getLanIp();
        }
        
        return $currentHost;
    }
    
    /**
     * Get the LAN IP address of the server
     */
    public static function getLanIp()
    {
        // Try to get LAN IP on Windows
        if (PHP_OS_FAMILY === 'Windows') {
            $output = shell_exec('ipconfig | findstr /i "IPv4"');
            if ($output) {
                // Extract IP from ipconfig output
                preg_match('/(\d+\.\d+\.\d+\.\d+)/', $output, $matches);
                if (isset($matches[1]) && $matches[1] !== '127.0.0.1') {
                    return $matches[1];
                }
            }
        } else {
            // For Linux/Mac
            $output = shell_exec("hostname -I | awk '{print $1}'");
            if ($output && trim($output) !== '127.0.0.1') {
                return trim($output);
            }
        }
        
        // Fallback: try to get IP from socket connection
        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if ($sock) {
            socket_connect($sock, "8.8.8.8", 53);
            socket_getsockname($sock, $name);
            socket_close($sock);
            if ($name && $name !== '127.0.0.1') {
                return $name;
            }
        }
        
        // Final fallback
        return '192.168.1.100'; // Common default
    }
    
    /**
     * Generate QR-friendly URL for table token
     */
    public static function getTableQrUrl($token)
    {
        $host = self::getQrHost();
        $port = request()->getPort();
        $scheme = request()->getScheme();
        
        // Build URL with proper host
        $url = $scheme . '://' . $host;
        
        // Add port if not standard
        if (($scheme === 'http' && $port !== 80) || ($scheme === 'https' && $port !== 443)) {
            $url .= ':' . $port;
        }
        
        $url .= '/table/' . $token;
        
        return $url;
    }
}
