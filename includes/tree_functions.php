<?php
require_once 'functions.php';

class NodePohon
{
    public $data;
    public $kiri;
    public $kanan;
    public $jumlahBuku;
    public $subgenre;
    public $level;
    public $parent;

    public function __construct($nilai, $jumlahBuku = 0, $level = 0)
    {
        $this->data = $nilai;
        $this->kiri = null;
        $this->kanan = null;
        $this->jumlahBuku = $jumlahBuku;
        $this->subgenre = [];
        $this->level = $level;
        $this->parent = null;
    }
}

class ItemPrioritas
{
    public $node;
    public $skorRelevasi;
    public $indeks;

    public function __construct($node, $skorRelevasi, $indeks)
    {
        $this->node = $node;
        $this->skorRelevasi = $skorRelevasi;
        $this->indeks = $indeks;
    }
}


class AntrianPrioritas
{
    private $items;

    public function __construct()
    {
        $this->items = [];
    }


    public function tambah($item)
    {
        $this->items[] = $item;
        $this->urutkanBerdasarkanPrioritas();
    }


    public function ambil()
    {
        if (empty($this->items)) {
            return null;
        }
        return array_shift($this->items);
    }

    public function kosong()
    {
        return empty($this->items);
    }

    private function urutkanBerdasarkanPrioritas()
    {
        usort($this->items, function ($a, $b) {
            if ($a->skorRelevasi == $b->skorRelevasi) {
                return $a->indeks - $b->indeks; // Jika sama, urutkan berdasarkan indeks
            }
            return $b->skorRelevasi - $a->skorRelevasi;
        });
    }

    public function ukuran()
    {
        return count($this->items);
    }
}

class PohonGenre
{
    private $akar;
    private $jumlahNode;
    private $allNodes; 

    public function __construct()
    {
        $this->akar = null;
        $this->jumlahNode = 0;
        $this->allNodes = [];
    }

    
    public function bangunTreeDariData()
    {
        $daftarGenre = getGenres();
        $bookCounts = getAllBookCounts();
        $this->allNodes = [];

        $this->akar = new NodePohon("Root", 0, 0);
        $this->allNodes[] = $this->akar;

        foreach ($daftarGenre as $namaGenre => $daftarSubgenre) {
            $jumlahBukuGenre = isset($bookCounts[$namaGenre]) ? $bookCounts[$namaGenre]['genre_count'] : 0;

            $nodeGenre = new NodePohon($namaGenre, $jumlahBukuGenre, 1);
            $nodeGenre->parent = $this->akar;
            $this->allNodes[] = $nodeGenre;
            $this->jumlahNode++;

            foreach ($daftarSubgenre as $namaSubgenre) {
                $jumlahBukuSubgenre = 0;
                if (isset($bookCounts[$namaGenre]['subgenres'][$namaSubgenre])) {
                    $jumlahBukuSubgenre = $bookCounts[$namaGenre]['subgenres'][$namaSubgenre];
                }

                $nodeSubgenre = new NodePohon($namaSubgenre, $jumlahBukuSubgenre, 2);
                $nodeSubgenre->parent = $nodeGenre;
                $this->allNodes[] = $nodeSubgenre;
                $this->jumlahNode++;
            }
        }
    }

    private function hitungSkorRelevasi($node, $kataPencarian)
    {
        if ($node->level == 1) {
            $skor = $node->jumlahBuku;
        } elseif ($node->level == 2) {
            $skor = $node->jumlahBuku;
        }

        return $skor;
    }

    public function cariBestFirst($kataPencarian)
    {
        $hasilPencarian = [];
        $antrianPrioritas = new AntrianPrioritas();
        $nodesDikunjungi = [];

        foreach ($this->allNodes as $indeks => $node) {
            if ($node->level > 0) { // Skip root node
                $skorRelevasi = $this->hitungSkorRelevasi($node, $kataPencarian);
            
                if ($skorRelevasi > 0) {
                    $itemPrioritas = new ItemPrioritas($node, $skorRelevasi, $indeks);
                    $antrianPrioritas->tambah($itemPrioritas);
                }
            }
        }

        while (!$antrianPrioritas->kosong()) {
            $itemSaatIni = $antrianPrioritas->ambil();
            $nodeSaatIni = $itemSaatIni->node;
            $nodesDikunjungi[] = $nodeSaatIni->data;
            $hasilPencarian[] = [
                'nama' => $nodeSaatIni->data,
                'level' => $nodeSaatIni->level,
                'jumlah_buku' => $nodeSaatIni->jumlahBuku,
                'parent' => $nodeSaatIni->parent ? $nodeSaatIni->parent->data : null,
                'skor_relevasi' => $itemSaatIni->skorRelevasi,
                'urutan_kunjungan' => count($nodesDikunjungi)
            ];
        }

        return [
            'hasil' => $hasilPencarian,
            'nodes_dikunjungi' => $nodesDikunjungi,
            'total_ditemukan' => count($hasilPencarian)
        ];
    }

    public function cariDFS($kataPencarian)
    {
        $hasilPencarian = [];
        $nodesDikunjungi = [];
        $urutanKunjungan = 0;

        $this->dfsPencarianDariRoot($this->akar, $kataPencarian, $hasilPencarian, $nodesDikunjungi, $urutanKunjungan);

        return [
            'hasil' => $hasilPencarian,
            'nodes_dikunjungi' => $nodesDikunjungi,
            'total_ditemukan' => count($hasilPencarian)
        ];
    }

    private function dfsPencarianDariRoot($node, $kataPencarian, &$hasilPencarian, &$nodesDikunjungi, &$urutanKunjungan)
    {
        if ($node === null) {
            return;
        }

        $urutanKunjungan++;
        $nodesDikunjungi[] = $node->data;

        if ($node->level > 0) {
            $skorRelevasi = $this->hitungSkorRelevasi($node, $kataPencarian);
            
            if ($skorRelevasi > 0) {
                $hasilPencarian[] = [
                    'nama' => $node->data,
                    'level' => $node->level,
                    'jumlah_buku' => $node->jumlahBuku,
                    'parent' => $node->parent ? $node->parent->data : null,
                    'skor_relevasi' => $skorRelevasi,
                    'urutan_kunjungan' => $urutanKunjungan
                ];
            }
        }

        $children = [];
        foreach ($this->allNodes as $childNode) {
            if ($childNode->parent === $node) {
                $children[] = $childNode;
            }
        }

        usort($children, function($a, $b) use ($kataPencarian) {
            $skorA = $this->hitungSkorRelevasi($a, $kataPencarian);
            $skorB = $this->hitungSkorRelevasi($b, $kataPencarian);
            
            if ($skorA == $skorB) {
                return strcmp($a->data, $b->data);
            }
            return $skorA - $skorB; 
        });

    
        foreach ($children as $childNode) {
            $this->dfsPencarianDariRoot($childNode, $kataPencarian, $hasilPencarian, $nodesDikunjungi, $urutanKunjungan);
        }
    }

    public function dapatkanSemuaNode()
    {
        return $this->allNodes;
    }

    public function dapatkanStatistik()
    {
        $totalGenre = 0;
        $totalSubgenre = 0;
        $totalBuku = 0;

        foreach ($this->allNodes as $node) {
            if ($node->level == 1) {
                $totalGenre++;
            } elseif ($node->level == 2) {
                $totalSubgenre++;
            }
            $totalBuku += $node->jumlahBuku;
        }

        return [
            'total_genre' => $totalGenre,
            'total_subgenre' => $totalSubgenre,
            'total_buku' => $totalBuku,
            'total_nodes' => $this->jumlahNode,
            'kedalaman_maksimum' => 2
        ];
    }
}

function searchGenresBestFirst($query)
{
    $tree = new PohonGenre();
    $tree->bangunTreeDariData();
    return $tree->cariBestFirst($query);
}

function searchGenresDFS($query)
{
    $tree = new PohonGenre();
    $tree->bangunTreeDariData();
    return $tree->cariDFS($query);
}

function buildGenreTree()
{
    $daftarGenre = getGenres();
    $bookCounts = getAllBookCounts();
    $strukturPohon = [];

    foreach ($daftarGenre as $namaGenre => $daftarSubgenre) {
        $strukturPohon[$namaGenre] = [
            'subgenres' => [],
            'book_count' => isset($bookCounts[$namaGenre]) ? $bookCounts[$namaGenre]['genre_count'] : 0
        ];

        foreach ($daftarSubgenre as $namaSubgenre) {
            $jumlahSubgenre = 0;
            if (isset($bookCounts[$namaGenre]['subgenres'][$namaSubgenre])) {
                $jumlahSubgenre = $bookCounts[$namaGenre]['subgenres'][$namaSubgenre];
            }

            $strukturPohon[$namaGenre]['subgenres'][$namaSubgenre] = [
                'book_count' => $jumlahSubgenre
            ];
        }
    }

    return $strukturPohon;
}

function getTreeStats()
{
    $tree = new PohonGenre();
    $tree->bangunTreeDariData();
    return $tree->dapatkanStatistik();
}

?>
