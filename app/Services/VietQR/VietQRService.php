<?php

namespace App\Services\VietQR;

use Illuminate\Support\Facades\Log;

class VietQRService
{
    private string $apiUrl;
    
    public function __construct()
    {
        $this->apiUrl = config('services.vietqr.api_url', 'https://img.vietqr.io/image');
    }
    
    /**
     * Generate QR code URL for payment
     *
     * @param string $bankCode Bank code (e.g., VCB, TCB, VPB, MB, etc.)
     * @param string $accountNo Bank account number
     * @param int $amount Amount in VND
     * @param string $addInfo Additional info (order description)
     * @param string $accountName Account holder name
     * @param string $template Template style (compact, compact2, print)
     * @return string|null QR code image URL
     */
    public function generateQR(
        string $bankCode,
        string $accountNo,
        int $amount,
        string $addInfo = '',
        string $accountName = '',
        string $template = 'compact2'
    ): ?string {
        try {
            // Clean account name
            $accountName = $this->cleanAccountName($accountName);
            
            // Clean addInfo
            $addInfo = $this->cleanAddInfo($addInfo);
            
            // Build base URL
            $baseUrl = $this->apiUrl . '/' . $bankCode . '-' . $accountNo . '-' . $template . '.png';
            
            // Build query parameters
            $queryParams = [
                'amount' => $amount,
                'addInfo' => $addInfo,
            ];
            
            // Add account name if provided
            if (!empty($accountName)) {
                $queryParams['accountName'] = $accountName;
            }
            
            // Build final URL with query parameters
            $qrUrl = $baseUrl . '?' . http_build_query($queryParams);
            
            return $qrUrl;
            
        } catch (\Exception $e) {
            Log::error('VietQR service error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Clean account name for QR code
     *
     * @param string $accountName
     * @return string
     */
    private function cleanAccountName(string $accountName): string
    {
        // Remove accents
        $accountName = $this->removeVietnameseAccents($accountName);
        
        // Remove special characters except spaces
        $accountName = preg_replace('/[^a-zA-Z0-9\s]/', '', $accountName);
        
        // Replace multiple spaces with single space
        $accountName = preg_replace('/\s+/', ' ', $accountName);
        
        return trim($accountName);
    }
    
    /**
     * Clean additional info for QR code
     *
     * @param string $addInfo
     * @return string
     */
    private function cleanAddInfo(string $addInfo): string
    {
        // Remove special characters except spaces
        $addInfo = preg_replace('/[^a-zA-Z0-9\s]/', '', $addInfo);
        
        // Replace multiple spaces with single space
        $addInfo = preg_replace('/\s+/', ' ', $addInfo);
        
        // Remove accents
        $addInfo = $this->removeVietnameseAccents($addInfo);
        
        // Limit to 25 characters
        $addInfo = mb_substr(trim($addInfo), 0, 25);
        
        // Replace remaining spaces with underscores for URL
        $addInfo = str_replace(' ', '_', $addInfo);
        
        return $addInfo ?: 'Thanh_toan_don_hang';
    }
    
    /**
     * Remove Vietnamese accents
     *
     * @param string $text
     * @return string
     */
    private function removeVietnameseAccents(string $text): string
    {
        $accents = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
            'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ',
            'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ',
            'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ',
            'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ',
            'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ',
            'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ',
            'Đ',
        ];
        
        $noAccents = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
            'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A',
            'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E',
            'I', 'I', 'I', 'I', 'I',
            'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O',
            'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U',
            'Y', 'Y', 'Y', 'Y', 'Y',
            'D',
        ];
        
        return str_replace($accents, $noAccents, $text);
    }
}

