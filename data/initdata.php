<?php
/**
 * 生成字典数据文件 data.json
 */
require 'vendor/autoload.php';


class initdata
{
    private $db;
    private $message = '生成结束!';

    public function __construct()
    {
        $this->database_type = 'mysql';
        $this->database_name = 'test';
        $this->host = 'rm-test.mysql.rds.aliyuncs.com';
        $this->username = 'test';
        $this->pwd = 'test';
        $this->charset = 'utf8';

        $this->db = new medoo([
            'database_type' => $this->database_type,
            'database_name' => $this->database_name,
            'server' => $this->host,
            'username' => $this->username,
            'password' => $this->pwd,
            'charset' => $this->charset
        ]);
    }

    public function init()
    {
        $this->makefile($this->getTables());

        return json_encode(['message'=>$this->message]);

    }

    private function getTables()
    {
        $data = $this->db->query("SHOW TABLE STATUS")->fetchAll();
        if (isset($data[0])) {
            $tables = array();
            $tableFields = array();
            $tableIndexes = array();
            foreach ($data as $item) {
                $tables[$item['Name']]['engine'] = $item['Engine'];
                $tables[$item['Name']]['comment'] = $item['Comment'];
                $tableFields[$item['Name']] = $this->getFieldsByTablename($item['Name']);
                $tableIndexes[$item['Name']] = $this->getIndexesByTablename($item['Name']);
            }
            return array(
                'tables' => $tables,
                'tableFields' => $tableFields,
                'tableIndexes' => $tableIndexes,
            );
        }
    }

    /**
     * 获取字段信息
     * @param string $tableName
     * @return bool
     */
    private function getFieldsByTablename($tableName = '')
    {
        if (empty($tableName)) return false;
        $sql = "SELECT ORDINAL_POSITION,COLUMN_NAME, COLUMN_TYPE, COLUMN_KEY, COLUMN_DEFAULT," .
            "COLUMN_COMMENT FROM information_schema.`COLUMNS` " .
            "WHERE TABLE_NAME = '" . $tableName . "' AND TABLE_SCHEMA='" . $this->database_name . "'";
        $data = $this->db->query($sql)->fetchAll();
        $fields = array();
        if (isset($data[0])) {
            foreach ($data as $key => $item) {
                $fields[$key]['column_name'] = $item['COLUMN_NAME'];
                $fields[$key]['column_comment'] = $item['COLUMN_COMMENT'];
                $fields[$key]['column_type'] = $item['COLUMN_TYPE'];
                $fields[$key]['column_default'] = $item['COLUMN_DEFAULT'];
                $fields[$key]['column_comment'] = $item['COLUMN_COMMENT'];
            }
        }
        return $fields;
    }

    /**
     * 获取索引信息
     * @param string $tableName
     * @return bool
     */
    private function getIndexesByTablename($tableName = '')
    {
        if (empty($tableName)) return false;
        $sql = "SHOW INDEX FROM " . $tableName . ";";
        $data = $this->db->query($sql)->fetchAll();
        $fields = array();
        if (isset($data[0])) {
            foreach ($data as $key => $item) {
                $fields[$key]['key_name'] = $item['Key_name'];
                $fields[$key]['seq_in_index'] = $item['Seq_in_index'];
                $fields[$key]['column_name'] = $item['Column_name'];
                $fields[$key]['non_unique'] = $item['Non_unique'];
                $fields[$key]['index_type'] = $item['Index_type'];
                $fields[$key]['index_comment'] = $item['Index_comment'];
            }
        }
        return $fields;
    }

    private function makefile($array)
    {
        if (is_array($array)) {
            file_put_contents('data.json', json_encode($array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }
}

$obj = new initdata();
echo $obj->init();
