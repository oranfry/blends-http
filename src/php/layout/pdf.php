<?php
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');

echo $filedata;
