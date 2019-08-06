<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class TagRepository {
    public function getMostExpensiveTags($spaceId, $limit = null, $year = null, $month = null) {
        $sql = '
            SELECT
                tags.name AS name,
                tags.color AS color,
                SUM(spendings.amount) AS amount,
                tags.id AS id,
                tags.space_id as space_id,
                Concat("/storage/category/", tags.image) as image,
                tags.type as type,
                tags.created_at,
                tags.updated_at,
                tags.deleted_at
            FROM
                tags
            LEFT OUTER JOIN
                spendings ON tags.id = spendings.tag_id AND spendings.deleted_at IS NULL
            WHERE
                tags.space_id = ?';

        if ($year) {
            $sql .= ' AND YEAR(happened_on) = ?';
        }

        if ($month) {
            $sql .= ' AND MONTH(happened_on) = ?';
        }

        $sql .= '
            GROUP BY
                tags.id
            ORDER BY
                SUM(spendings.amount) DESC
        ';

        if ($limit) {
            $sql .= ' LIMIT ' . $limit;
        }

        $data = [$spaceId];

        if ($year) {
            $data[] = $year;
        }

        if ($month) {
            $data[] = $month;
        }

        return DB::select($sql . ';', $data);
    }
}
