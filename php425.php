#!/usr/bin/env php
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

php425::setIncludePath();
require_once('Console/CommandLine.php');
require_once('ARGF.php');
require_once('libs/phpParser.php');

class php425
{
    public static function setIncludePath()
    {
        $vendor_dir = self::createPath(dirname(__FILE__), 'vendor');

        $include_paths = array(
            '.',
            self::createPath($vendor_dir, 'ARGF'),
            self::createPath($vendor_dir, 'pear', 'php'));

        $include_paths = array_unique(array_merge(
            $include_paths, split(':', get_include_path())));
        set_include_path(join(':', $include_paths));
    }

    protected static function createPath()
    {
        return join(func_get_args(), DIRECTORY_SEPARATOR);
    }

    public function execute()
    {
        $result = $this->parseOptions();
        $options =& $result['options'];
        $args =& $result['args'];

        if ($options['recursive']) {
            return $this->executeRecursively(array_pop($args), $args, $options);
        }

        if ($options['in_place'] && $args) {
            foreach ($args as $file) {
                if (!is_file($file)) {
                    fprintf(STDERR, "No such file: $file\n");
                    continue;
                }

                $contents = file_get_contents($file);
                file_put_contents($file, print_r(
                    $this->migrate($contents, $options['format']),
                    true));
            }
        } else {
            $argf = new ARGF($args);
            print_r($this->migrate($argf->toString(), $options['format']));
        }

        return 0;
    }

    protected function echoFlush($string)
    {
        echo $string;
        flush();
        ob_flush();
    }

    protected function executeRecursively($target_dir, $sources, $options)
    {
        $mode = 0777 ^ umask();
        @mkdir($target_dir, $mode, true);

        $regexp = '/\.('.join('|', $options['suffixes']).')$/';
        $pairs = array();
        foreach ($sources as $source) {
            if (is_dir($source)) {
                $pwd = getcwd();
                chdir($source);
                foreach ($this->find() as $entry) {
                    if (is_dir($entry)) {
                        continue;
                    }
                    $action = preg_match($regexp, $entry) ? 'migrate' : 'copy';
                    $pairs[] = array(
                        'action' => $action,
                        'source' => "$source/".substr($entry, 2),
                        'target' => basename($source).'/'.dirname($entry));
                }
                chdir($pwd);
            } else {
                $pairs[] = array(
                    'action' => 'migrate', 'source' => $source,
                    'target' => null);
            }
        }

        foreach ($pairs as $pair) {
            $action = $pair['action'];
            $source = $pair['source'];
            $target = $pair['target'];

            if ($target) {
                @mkdir("$target_dir/$target", $mode, true);
            }

            $dest = "$target_dir/$target/".basename($source);
            if (file_exists($dest)) {
                $this->echoFlush("skipped $source ... file exists\n");
                continue;
            }

            switch ($action) {
            case 'copy':
                $this->echoFlush("copying $source ... ");
                file_put_contents($dest, file_get_contents($source));
                break;
            case 'migrate':
                $this->echoFlush("migrating $source ... ");
                $contents = file_get_contents($source);
                file_put_contents(
                    $dest, $this->migrate($contents, $options['format']));
                break;
            }
            $this->echoFlush("done\n");
        }

        return 0;
    }

    protected function find($directory = null)
    {
        if (!$directory) {
            $directory = '.';
        }

        $entries = array();
        foreach (scandir($directory) as $entry) {
            if ($entry === '.' | $entry === '..') {
                continue;
            }

            $path = "$directory/$entry";
            $entries[] = $path;

            if (is_dir($path)) {
                $entries = array_merge($entries, $this->find($path));
            }
        }

        return $entries;
    }

    protected function &migrate(&$source, $format)
    {
        switch ($format) {
        case 'nodes':
            return phpParser::toNodes($source);
        case 'string':
            return phpParser::toString($source);
        case 'terminals':
            return phpParser::toTerminals($source);
        case 'tokens':
            return phpParser::toTokens($source);
        case 'tree':
            return phpParser::toTree($source);
        default:
            return false;
        }
    }

    protected function parseOptions()
    {
        $argf = new ARGF();
        $argc =& $argf->argc();
        $argv =& $argf->argv();

        $optparser = new Console_CommandLine(array(
            'name'        => basename($argv[0]),
            'description' =>
                'A php migratory tool from 4 to 5 written in pure php',
            'version'     => '0.0.1'));

        $optparser->addOption('format', array(
            'choices'     => array(
                'nodes', 'string', 'terminals', 'tokens', 'tree'),
            'default'     => 'string',
            'description' => 'format of output (default: string)',
            'long_name'   => '--format',
            'short_name'  => '-f'));

        $optparser->addOption('in_place', array(
            'action'      => 'StoreTrue',
            'description' => 'edit files in place',
            'long_name'   => '--in-place',
            'short_name'  => '-i'));

        $optparser->addOption('recursive', array(
            'action'      => 'StoreTrue',
            'description' => 'migrate recursively',
            'long_name'   => '--recursive',
            'short_name'  => '-r'));

        $optparser->addOption('suffixes', array(
            'default'     => 'php,php4,php5,inc',
            'description' =>
                'suffixes of files to be migrated (default: php,php4,php5,inc)',
            'long_name'   => '--suffixes',
            'short_name'  => '-s'));

        $optparser->addArgument('files', array('multiple' => true));

        try {
            $result = $optparser->parse();
        } catch (Exception $e) {
            $optparser->displayError($e->getMessage());
        }

        $options =& $result->options;
        $args =& $result->args['files'];

        if ($options['recursive']) {
            if (count($args) < 2) {
                $optparser->displayError(
                    'target directory must be specified with -r');
            }

            $target = $args[count($args)-1];
            if (!is_dir($target) && file_exists($target)) {
                $optparser->displayError(
                    'target directory cannot be a regular file');
            }
        }

        $options['suffixes'] = split(',', $options['suffixes']);

        return array('options' => $options, 'args' => $args);
    }
}

$php425 = new php425();
exit($php425->execute());
