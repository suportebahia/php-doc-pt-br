<?php

declare(strict_types=1);

require_once __DIR__ . '/validate-coverage-matrix.php';

exit(CoverageMatrixValidator::selfTest());
