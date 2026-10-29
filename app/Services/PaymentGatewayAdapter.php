<?php
namespace InProsper\Services;
/** Adapter simulasi. Ganti implementasi ini dengan REST API + webhook setelah vendor dipilih. */
final class PaymentGatewayAdapter {
    public function simulate(string $result): array {
        $success = $result === 'success';
        return ['success'=>$success, 'status'=>$success?'berhasil':'gagal', 'reference'=>'SIM-PAY-'.bin2hex(random_bytes(5))];
    }
}
