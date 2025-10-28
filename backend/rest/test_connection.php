<?php
require_once 'dao/BaseDao.php';
$db = new BaseDAO("recipes");
if($db){
    echo "✅ Database connected successfully!";
} else {
    echo "❌ Connection failed: " . $db->getLastError();
}