<?php
class SaranGenerator {
    private $jenisKankerData;
    private $gejalaData;
    private $kankerGejalaMapping;

    public function __construct() {
        $this->jenisKankerData = [];
        $this->gejalaData = [];
        $this->kankerGejalaMapping = [];
    }

    /**
     * Hitung skor kecocokan gejala user dengan jenis kanker tertentu
     */
    public function hitungSkorKecocokan($gejalaUserArray, $jenisKanker)
    {
        if (!isset($this->kankerGejalaMapping[$jenisKanker])) {
            return 0;
        }

        $gejalaKankerIds = $this->kankerGejalaMapping[$jenisKanker];
        $totalGejalaKanker = count($gejalaKankerIds);

        if ($totalGejalaKanker == 0) {
            return 0;
        }

        $gejalaUserNormalized = array_map('strtolower', $gejalaUserArray);
        $gejalaUserNormalized = array_map('trim', $gejalaUserNormalized);

        $jumlahKecocokan = 0;

        foreach ($gejalaKankerIds as $gejalaId) {
            if (isset($this->gejalaData[$gejalaId])) {
                $namaGejala = strtolower(trim($this->gejalaData[$gejalaId]));

                foreach ($gejalaUserNormalized as $gejalaUser) {
                    if ($this->isGejalaMatch($gejalaUser, $namaGejala)) {
                        $jumlahKecocokan++;
                        break;
                    }
                }
            }
        }

        $skorKecocokan = ($jumlahKecocokan / $totalGejalaKanker) * 100;
        return round($skorKecocokan, 1);
    }

    /**
     * Cek apakah gejala user cocok dengan gejala di database
     */
    private function isGejalaMatch($gejalaUser, $namaGejala)
    {
        // Exact match
        if ($gejalaUser === $namaGejala) {
            return true;
        }

        // Partial match - mengecek apakah gejala user mengandung kata kunci dari nama gejala
        $kataKunciGejala = explode(' ', $namaGejala);
        $kataKunciUser = explode(' ', $gejalaUser);

        $matchCount = 0;
        $totalKataKunci = 0;

        foreach ($kataKunciGejala as $kata) {
            $kata = trim($kata);
            if (strlen($kata) > 2) { // Skip kata pendek
                $totalKataKunci++;
                foreach ($kataKunciUser as $kataUser) {
                    $kataUser = trim($kataUser);
                    if (strpos($kataUser, $kata) !== false || strpos($kata, $kataUser) !== false) {
                        $matchCount++;
                        break;
                    }
                }
            }
        }

        // Jika tidak ada kata kunci yang valid, lakukan exact comparison
        if ($totalKataKunci == 0) {
            return strpos($gejalaUser, $namaGejala) !== false || strpos($namaGejala, $gejalaUser) !== false;
        }

        // Jika minimal 60% kata kunci cocok
        return ($matchCount / $totalKataKunci) >= 0.6;
    }

    /**
     * Generate saran berdasarkan jenis kanker dan skor kecocokan
     */
    public function generateSaran($jenisKanker, $skorKecocokan, $gejalaUser = [])
    {
        if (!isset($this->jenisKankerData[$jenisKanker])) {
            return "Jenis kanker tidak ditemukan dalam database.";
        }

        $namaKanker = $this->jenisKankerData[$jenisKanker]['nama'];

        // Tentukan tingkat risiko
        if ($skorKecocokan >= 70) {
            $tingkatRisiko = 'tinggi';
            $warna = '🔴';
            $judul = 'RISIKO TINGGI - SEGERA KONSULTASI DOKTER';
            $deskripsi = 'Gejala yang Anda alami menunjukkan tingkat kecocokan tinggi dengan ' . $namaKanker . '. SEGERA konsultasi ke dokter spesialis untuk pemeriksaan lebih lanjut.';
            $tindakan = [
                '🚨 Segera buat janji dengan dokter spesialis',
                '🔬 Persiapkan untuk pemeriksaan penunjang (CT-Scan, MRI, biopsi)',
                '📋 Siapkan riwayat kesehatan lengkap keluarga',
                '⚠️ Jangan menunda pemeriksaan lebih dari 3 hari'
            ];
        } elseif ($skorKecocokan >= 40) {
            $tingkatRisiko = 'sedang';
            $warna = '🟡';
            $judul = 'RISIKO SEDANG - KONSULTASI DOKTER DIANJURKAN';
            $deskripsi = 'Beberapa gejala menunjukkan kemungkinan yang perlu diwaspadai. Disarankan konsultasi ke dokter dalam 1-2 minggu.';
            $tindakan = [
                '👨‍⚕️ Konsultasi ke dokter umum atau spesialis',
                '🔍 Lakukan pemeriksaan fisik menyeluruh',
                '📝 Catat perkembangan gejala harian',
                '🛡️ Hindari faktor risiko yang diketahui'
            ];
        } else {
            $tingkatRisiko = 'rendah';
            $warna = '🟢';
            $judul = 'RISIKO RENDAH - PEMANTAUAN RUTIN';
            $deskripsi = 'Risiko ' . $namaKanker . ' tergolong rendah berdasarkan gejala saat ini. Tetap jaga kesehatan dan lakukan pemeriksaan rutin.';
            $tindakan = [
                '✅ Pemeriksaan kesehatan rutin 6-12 bulan sekali',
                '🏃‍♂️ Pertahankan pola hidup sehat',
                '🥗 Konsumsi makanan bergizi seimbang',
                '📱 Pantau dan catat perubahan gejala'
            ];
        }

        // Format saran
        $saran = "═══════════════════════════════════════\n";
        $saran .= "🏥 HASIL ANALISIS GEJALA KANKER\n";
        $saran .= "═══════════════════════════════════════\n\n";

        $saran .= "📊 Tingkat Kecocokan: " . $skorKecocokan . "%\n";
        $saran .= "🎯 Jenis: " . $namaKanker . "\n\n";

        $saran .= $warna . " " . $judul . "\n";
        $saran .= $deskripsi . "\n\n";

        $saran .= "📝 REKOMENDASI TINDAKAN:\n";
        foreach ($tindakan as $item) {
            $saran .= "   " . $item . "\n";
        }

        // Gejala yang cocok
        if (!empty($gejalaUser) && isset($this->kankerGejalaMapping[$jenisKanker])) {
            $gejalaKankerIds = $this->kankerGejalaMapping[$jenisKanker];
            $gejalaCocok = [];

            foreach ($gejalaKankerIds as $gejalaId) {
                if (isset($this->gejalaData[$gejalaId])) {
                    $namaGejala = $this->gejalaData[$gejalaId];
                    foreach ($gejalaUser as $gejalaUserItem) {
                        if ($this->isGejalaMatch(strtolower(trim($gejalaUserItem)), strtolower(trim($namaGejala)))) {
                            $gejalaCocok[] = $namaGejala;
                            break;
                        }
                    }
                }
            }

            if (!empty($gejalaCocok)) {
                $saran .= "\n🔍 GEJALA YANG COCOK:\n";
                foreach ($gejalaCocok as $gejala) {
                    $saran .= "   ✓ " . $gejala . "\n";
                }
            }
        }

        // Peringatan tambahan
        $saran .= "\n" . $this->getPeringatanTambahan($tingkatRisiko, $namaKanker) . "\n";

        // Disclaimer
        $saran .= "\n⚠️  DISCLAIMER PENTING:\n";
        $saran .= "   • Hasil ini hanya sebagai panduan awal, bukan diagnosis medis\n";
        $saran .= "   • Selalu konsultasikan dengan dokter untuk diagnosis akurat\n";
        $saran .= "   • Jangan mengabaikan gejala yang berkelanjutan atau memburuk\n";
        $saran .= "   • Sistem ini tidak menggantikan pemeriksaan medis profesional\n";

        return $saran;
    }

    /**
     * Get peringatan tambahan berdasarkan tingkat risiko
     */
    private function getPeringatanTambahan($tingkatRisiko, $namaKanker)
    {
        switch ($tingkatRisiko) {
            case 'tinggi':
                return "🔥 URGENSI TINGGI: Deteksi dini adalah kunci keberhasilan pengobatan " . $namaKanker . "!";
            case 'sedang':
                return "⚡ PERHATIAN: Pemeriksaan dini dapat mencegah perkembangan " . $namaKanker . ".";
            default:
                return "✨ PENCEGAHAN: Gaya hidup sehat adalah kunci mencegah " . $namaKanker . ".";
        }
    }

    /**
     * Analisis multiple kanker dan return top N hasil
     */
    public function analisisMultipleKanker($gejalaUser, $topN = 5)
    {
        $hasilAnalisis = [];

        foreach ($this->jenisKankerData as $kankerID => $data) {
            $skor = $this->hitungSkorKecocokan($gejalaUser, $kankerID);
            if ($skor > 0) {
                $hasilAnalisis[] = [
                    'id' => $kankerID,
                    'nama' => $data['nama'],
                    'skor' => $skor
                ];
            }
        }

        // Sort berdasarkan skor tertinggi
        usort($hasilAnalisis, function ($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        return array_slice($hasilAnalisis, 0, $topN);
    }

    // Getter dan Setter methods
    public function getJenisKankerData()
    {
        return $this->jenisKankerData;
    }

    public function setJenisKankerData($jenisKankerData)
    {
        $this->jenisKankerData = $jenisKankerData;
    }

    public function getGejalaData()
    {
        return $this->gejalaData;
    }

    public function setGejalaData($gejalaData)
    {
        $this->gejalaData = $gejalaData;
    }

    public function getKankerGejalaMapping()
    {
        return $this->kankerGejalaMapping;
    }

    public function setKankerGejalaMapping($kankerGejalaMapping)
    {
        $this->kankerGejalaMapping = $kankerGejalaMapping;
    }
}
?>