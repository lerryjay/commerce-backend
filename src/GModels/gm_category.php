<?php
class GMCategory extends GModel
{
    private $mtablename = 'categories';

    public function addCategory($name, $description)
    {
        return $this->db->insert($this->mtablename, [
            'name' => $name,
            'description' => $description,
        ]);
    }
    public function updateCategory($categoryId, $name, $description)
    {
        return $this->db->update(
            $this->mtablename,
            [
                'name' => $name,
                'description' => $description,
            ],
            [
                'id' => $categoryId,
            ]
        );
    }
    private function getcategories($fields = [])
    {
        $this->db->query(
            $this->mtablename,
            array_merge($fields, [
                'categories.name AS categoryname',
                'categories.description',
            ])
        );
    }

    public function addParentRelation($parent, $sub)
    {
        return $this->db->insert('categorycategory', [
            'root_id' => $parent,
            'sub_id' => $sub,
        ]);
    }
    public function getCategoryRelation($root, $sub, $status = 1)
    {
        $this->db->query('categorycategory', ['root_id', 'sub_id']);
        $this->db->where_equal('(categorycategory.root_id', $root);
        $this->db->and_where('categorycategory.sub_id)', $sub);
        $this->db->or_where('(categorycategory.root_id', $sub);
        $this->db->and_where('categorycategory.sub_id', $root);
        $this->db->and_where('categorycategory.status', $status);
        return $this->db->exec()->rows;
    }
    public function getMainCategories($status = 1)
    {
        $this->getcategories(['categories.id AS categoryid']);
        $this->db->join('left', [
            [
                'table' => 'categorycategory',
                'field' => 'sub_id',
            ],
            [
                'table' => 'categories',
                'field' => 'id',
            ],
        ]);
        return $this->db
            ->where_equal('categories.status', $status)
            ->and_where(
                '( SELECT COUNT(*) FROM categorycategory WHERE categorycategory.sub_id = categories.id)',
                0
            )
            ->exec()->rows;
    }

    public function getSubCategory($categoryId, $status = 1)
    {
        $this->getcategories();
        $this->db->join('inner', [
            [
                'table' => 'categorycategory',
                'field' => 'sub_id',
            ],
            [
                'table' => 'categories',
                'field' => 'id',
            ],
        ]);
        $this->db->where_equal('categories.status', $status);
        $this->db->and_where('categorycategory.status', $status);
        $this->db->and_where('categorycategory.root_id', $categoryId);
        return $this->db->exec()->rows;
    }
    public function getParentCategory($categoryId, $status = 1)
    {
        $this->getcategories();
        $this->db->join('inner', [
            [
                'table' => 'categorycategory',
                'field' => 'root_id',
            ],
            [
                'table' => 'categories',
                'field' => 'id',
            ],
        ]);
        $this->db->where_equal('categories.status', $status);
        $this->db->and_where('categorycategory.status', $status);
        $this->db->and_where('categorycategory.root_id', $categoryId);
        return $this->db->exec()->rows;
    }

    public function getCategoryById($categoryId, $status = 1)
    {
        $this->getcategories();
        $this->db->where_equal('categories.status', $status);
        $this->db->and_where('categories.id', $categoryId);
        return $this->db->exec()->row;
    }

    public function changeCategoryStatus($categoryId, $status = 2)
    {
        return $this->db->update(
            $this->mtablename,
            [
                'status' => $status,
            ],
            [
                'id' => $categoryId,
            ]
        );
    }
}
?>
