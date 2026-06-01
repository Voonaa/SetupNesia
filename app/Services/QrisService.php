<?php

namespace App\Services;

use App\Models\Order;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrisService
{
    /**
     * Merchant ID Dana (nomor HP Dana merchant).
     * Format: 62 + nomor HP tanpa 0 di depan
     */
    protected string $merchantId;
    protected string $merchantName;
    protected string $merchantCity;
    protected string $merchantCode;

    public function __construct()
    {
        // Dana merchant: 0895629350777 → 6295629350777
        $danaNumber = config('services.qris.dana_number', '0895629350777');
        $this->merchantId   = '6' . ltrim($danaNumber, '0');
        $this->merchantName = config('services.qris.merchant_name', 'SetupNesia');
        $this->merchantCity = config('services.qris.merchant_city', 'Jakarta');
        $this->merchantCode = config('services.qris.merchant_code', '5999'); // 5999 = Misc. General Merchandise
    }

    /**
     * Generate a QRIS EMV string compliant with QRIS Indonesia standard (Bank Indonesia).
     * This string can be scanned by GoPay, OVO, Dana, LinkAja, ShopeePay, and all Indonesian banking apps.
     *
     * @param  Order   $order
     * @return string  Base64-encoded PNG image of the QR code
     */
    public function generateQrisImage(Order $order): string
    {
        $qrisString = $this->buildQrisString((int) $order->total_price, $order->order_number);

        $svg = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->errorCorrection('M')
            ->generate($qrisString);

        return base64_encode($svg);
    }

    /**
     * Build EMV QRIS string per Bank Indonesia standard.
     *
     * @param  int    $amount     Amount in IDR (integer)
     * @param  string $orderRef   Order reference / order number
     * @return string             Full QRIS EMV string with CRC
     */
    public function buildQrisString(int $amount, string $orderRef = ''): string
    {
        // EMV QR Code Specification for Payment Systems (QRIS Indonesia)
        $payload = '';

        // 00: Payload Format Indicator (mandatory, always "01")
        $payload .= $this->tlv('00', '01');

        // 01: Point of Initiation Method
        // "11" = static QR, "12" = dynamic QR (with amount)
        $payload .= $this->tlv('01', '12');

        // 26: Merchant Account Information (Dana/QRIS)
        // Sub-TLV for acquirer
        $merchantAccount  = $this->tlv('00', 'ID.CO.DANA.WWW');     // AID (Application Identifier)
        $merchantAccount .= $this->tlv('01', $this->merchantId);     // Merchant ID
        $payload .= $this->tlv('26', $merchantAccount);

        // 52: Merchant Category Code
        $payload .= $this->tlv('52', $this->merchantCode);

        // 53: Transaction Currency (360 = IDR)
        $payload .= $this->tlv('53', '360');

        // 54: Transaction Amount
        if ($amount > 0) {
            $payload .= $this->tlv('54', (string) $amount);
        }

        // 55: Tip or Convenience Indicator (01 = no tip)
        $payload .= $this->tlv('55', '01');

        // 58: Country Code (ID = Indonesia)
        $payload .= $this->tlv('58', 'ID');

        // 59: Merchant Name
        $payload .= $this->tlv('59', mb_substr($this->merchantName, 0, 25));

        // 60: Merchant City
        $payload .= $this->tlv('60', mb_substr($this->merchantCity, 0, 15));

        // 62: Additional Data Field Template (Order Reference)
        if (!empty($orderRef)) {
            $additionalData  = $this->tlv('05', mb_substr($orderRef, 0, 25)); // 05 = Reference Label
            $payload .= $this->tlv('62', $additionalData);
        }

        // 63: CRC (placeholder, always 4 digits)
        // CRC16-CCITT (polynomial 0x1021)
        $payload .= '6304'; // Tag 63 + Length 04, value to be computed

        // Compute CRC over entire string including "6304"
        $crc = $this->crc16($payload);

        return $payload . strtoupper($crc);
    }

    /**
     * Format a TLV (Tag-Length-Value) element for EMV QRIS.
     *
     * @param  string $tag    2-char EMV tag
     * @param  string $value  Value string
     * @return string         Formatted TLV
     */
    protected function tlv(string $tag, string $value): string
    {
        $length = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
        return $tag . $length . $value;
    }

    /**
     * Compute CRC16-CCITT checksum (used in EMV QRIS standard).
     *
     * @param  string $data
     * @return string  4-char hex CRC (uppercase)
     */
    protected function crc16(string $data): string
    {
        $crc = 0xFFFF;
        $polynomial = 0x1021;

        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= (ord($data[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return str_pad(dechex($crc), 4, '0', STR_PAD_LEFT);
    }
}
