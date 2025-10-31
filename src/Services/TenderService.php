<?php

namespace TenderService\Services;

use TenderService\Database\Connection;
use TenderService\Models\Tender;
use TenderService\Models\TenderDoc;
use PDOException;

class TenderService
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function saveTenders(array $tendersData): array
    {
        $results = [
            'total_processed' => 0,
            'tenders_added' => 0,
            'docs_added' => 0,
            'errors' => []
        ];

        foreach ($tendersData as $index => $tenderData) {
            try {
                $this->db->beginTransaction();

                // Сохраняем тендер
                $tenderId = $this->saveSingleTender($tenderData);
                
                if ($tenderId) {
                    $results['tenders_added']++;
                    
                    // Сохраняем документы
                    $docsAdded = $this->saveTenderDocs($tenderId, $tenderData['docs'] ?? []);
                    $results['docs_added'] += $docsAdded;
                }

                $this->db->commit();
                $results['total_processed']++;

            } catch (PDOException $e) {
                $this->db->rollBack();
                $results['errors'][] = "Tender {$tenderData['purchase_number']}: {$e->getMessage()}";
            }
        }

        return $results;
    }

    private function saveSingleTender(array $tenderData): ?int
    {
        $sql = "INSERT INTO tenders (
            purchase_number, object_info, max_price, currency_code,
            published_at, collecting_finished_at, purchase_type,
            region, stage, customers, ikzs, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
            object_info = VALUES(object_info),
            max_price = VALUES(max_price),
            currency_code = VALUES(currency_code),
            published_at = VALUES(published_at),
            collecting_finished_at = VALUES(collecting_finished_at),
            purchase_type = VALUES(purchase_type),
            region = VALUES(region),
            stage = VALUES(stage),
            customers = VALUES(customers),
            ikzs = VALUES(ikzs)";

        $params = [
            $tenderData['purchase_number'],
            $tenderData['object_info'] ?? null,
            $tenderData['max_price'] ?? null,
            $tenderData['currency_code'] ?? null,
            $tenderData['published_at'] ?? null,
            $tenderData['collecting_finished_at'] ?? null,
            $tenderData['purchase_type'] ?? null,
            $tenderData['region'] ?? null,
            $tenderData['stage'] ?? null,
            json_encode($tenderData['customers'] ?? []),
            json_encode($tenderData['ikzs'] ?? [])
        ];

        $this->db->execute($sql, $params);

        // Получаем ID тендера (нового или существующего)
        $result = $this->db->query(
            "SELECT id FROM tenders WHERE purchase_number = ?",
            [$tenderData['purchase_number']]
        );

        return $result[0]['id'] ?? null;
    }

    private function saveTenderDocs(int $tenderId, array $docs): int
    {
        $docsAdded = 0;

        foreach ($docs as $docData) {
            $sql = "INSERT INTO tender_docs (tender_id, doc_type, published_at)
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                        published_at = VALUES(published_at)";

            $this->db->execute($sql, [
                $tenderId,
                $docData['doc_type'],
                $docData['published_at'] ?? null
            ]);

            $docsAdded++;
        }

        return $docsAdded;
    }

    public function getTendersCount(): int
    {
        $result = $this->db->query("SELECT COUNT(*) as count FROM tenders");
        return (int)($result[0]['count'] ?? 0);
    }

    public function getDocsCount(): int
    {
        $result = $this->db->query("SELECT COUNT(*) as count FROM tender_docs");
        return (int)($result[0]['count'] ?? 0);
    }
}
