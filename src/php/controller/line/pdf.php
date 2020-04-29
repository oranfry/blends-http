<?php
define('LAYOUT', 'pdf');

$linetype = Linetype::load(LINETYPE_NAME);
$line = @$linetype->find_lines([(object)['field' => 'id', 'value' => LINE_ID]])[0];

if (!$line) {
    error_response('No such line', 400);
}

$linetype->load_children($line);

$cmd = "/usr/bin/xvfb-run -- /usr/bin/wkhtmltopdf -s A4 - -";
$descriptorspec = [
   ['pipe', 'r'],
   ['pipe', 'w'],
];

$process = proc_open($cmd, $descriptorspec, $pipes, '/tmp');

if (!is_resource($process)) {
    error_response('Failed to create pdf (1)');
}

fwrite($pipes[0], $linetype->ashtml($line));
fclose($pipes[0]);

$filedata = stream_get_contents($pipes[1]);
fclose($pipes[1]);

$return_value = proc_close($process);

return [
    'filedata' => $filedata,
    'filename' => LINE_ID . '.pdf',
];

