<?php

require_once __DIR__ . '/../dao/ReviewDAO.php';
require_once 'BaseService.php';

class ReviewService extends BaseService {

    public function __construct() {
        parent::__construct(new ReviewDAO());
    }
}
