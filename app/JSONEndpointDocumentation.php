<?php namespace App;

/**
 * This class (for PHP/Laravel 5) scans the DOCBLOCK comments for a function and generates a JSON-able object
 * that contains an index of public functions and various data that is for use by developers to consume a public API.
 *
 * In other words, if you have Endpoints in a class such as:
 * /api
 * /api/posts
 * /api/post?id=5
 * then you could call the createJSONForAllClassFunctions function on the index function (ie, /api) and return the
 * JSON version of the data. This acts as a GET-able index of the functions and descriptions of their use.
 *
 * Versions:
 *
 * 0.0.5 Updated documentation and allowed documentTypes.
 *
 * 0.0.4 Noticed an issue with the original regex that extracted the phpdoc:
 * (\/\*\*)([^*][^\/]*)(\*[\/])(\s*)(\w*)\s(function)\s(\w*)\(
 * It would skip docblocks that had urls in them. Reworked to overcome that bug.
 * Also fixed camelCase bug.
 * Todo: Auto-pop summary + description?
 *
 * 0.0.3 Add auto url, summary, description
 *
 * 0.0.2: Started as Trait, but the pollution of names in the class the trait was used in
 * bothered me, so changed to an instance class.
 *
 * 0.0.1: Initially we tried using ReflectorClass and getDocComment to get at the function comments,
 * however when used on a production server with opcache enabled (and what production server isn't using an opcache these days?)
 * the comments may be stripped. Problem. The next obvious solution thus is to read the source file and regex out the
 * comments ourselves.
 */

//
class JSONEndpointDocumentation
{
    private $semanticVersion = "0.0.5";

    /**
     * @var array
     */
    private $documentTypes = [
        'API' => [],
        'Copyright' => [],
        'URL' => [],
        'Title' => [],
        'Summary' => [],
        'Description' => [],
        'Notes' => [],
        'Version' => [],
        'Semantic Version' => [],
        'Method' => [
            'values' => ['GET', 'POST', 'DELETE', 'PUT', 'HEAD', 'OPTIONS', 'PATCH']
        ],
        'Url Params Required' => [],
        'Url Params Optional' => [],
        'Data Params' => [],
        'Success Response' => [],
        'Error Response' => [],
        'Sample Call' => [],
        'Cached' => [
            'values' => ['NO', 'YES']
        ],
        "Authentication" => [
            'values' => ['', 'None', 'Basic', 'Digest', 'NTLM']
        ],
    ];

    /**
     * @param $string
     * @return mixed
     */
    private function toCamelCase(string $string): string
    {
        if (strlen($string) == 0) {
            return $string;
        }

        $string = preg_replace('/[^0-9a-zA-Z ]/', '', $string);

        $index = 0;
        $string = preg_replace_callback('/\w\S*/', function ($matches) use (&$index) {
            $word = $matches[0];
            if ($index++ === 0) {
                return strtolower($word);
            } else {
                return strtoupper(substr($word, 0, 1)) . strtolower(substr($word, 1));
            }
        }, $string);

        $string = preg_replace('/ /', '', $string);
        return $string;
    }

    /**
     *
     */
    private function generateCamelCaseForDocumentTypes()
    {
        foreach ($this->documentTypes as $key => $documentType) {
            $this->documentTypes[$key]['name'] = $this->toCamelCase($key);
        };
    }

    private function validateValue(string $string, string $documentType): string
    {
        if (isset($this->documentTypes[$documentType])) {
            if (isset($this->documentTypes[$documentType]['values'])) {
                if (in_array($string, $this->documentTypes[$documentType]['values'])) {
                    return $string;
                } else {
                    return "";
                }
            }
        }
        return $string;
    }

    private function loadClassAsArray(string $class): array
    {
        $ref = new \ReflectionClass($class);
        $file = $ref->getFileName();
        $contents = file_get_contents($file);

        /*
         * This regex finds the comment section before any function and returns an array ...
         * Tested on regex101.com
         */
        $regex = '~(\/\*(\*(?!\/)|[^\*])*\*\/)\s*(\w*)\s(function)\s(\w*)\(~';
        preg_match_all($regex, $contents, $matches);

      //  $validDocumentTypes = array_column($this->documentTypes, 'name');

        $functions = [];

        foreach ($matches[1] as $key => $comment) {
            $comment = trim(substr($comment, 2, -2));
            $accessModifier = $matches[3][$key];
            $functionName = $matches[5][$key];

            $functions[$key] = [
                'key'=>$key,
                'functionName' => $functionName,
                'accessModifier' => $accessModifier,
                'comment' => $comment
            ];
        }

        return $functions;
    }

    /**
     * Note this function is heavy. It should be cached in the controller by whatever mechanism other api
     * calls are being cached.
     *
     * @param string $class
     * @return array
     */
    public function createJSONForAllClassFunctions(string $class, string $url = "/"): array
    {
        $this->generateCamelCaseForDocumentTypes();

        $validDocumentTypes = array_column($this->documentTypes, 'name');

        $results=[];

        $matches = $this->loadClassAsArray($class);
        foreach ($matches as $key => $arr) {
            $comment = $arr['comment'];
            $accessModifier = $arr['accessModifier'];
            $functionName = $arr['functionName'];

            if ($accessModifier == 'public' && !in_array($functionName, ['__construct'])) {

                if (!isset($results[$functionName])) {
                    if ($functionName == 'index') {
                        $results[$functionName] = [
                            'url' => "{$url}"];
                    } else {
                        $results[$functionName] = [
                            'url' => "{$url}{$functionName}"];
                    }
                };

                $lines = explode("\n", trim($comment));
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (substr($line, 0, 3) == "* @") {
                        $a = explode(" ", substr($line, 3));
                        $documentType = array_shift($a);

                        if (in_array($documentType, $validDocumentTypes)) {

                            $documentString = $this->validateValue(implode(" ", $a), $documentType);

                            if (isset($results[$functionName][$documentType])) {
                                if (is_array($results[$functionName][$documentType])) {
                                    $results[$functionName][$documentType][] = $documentString;
                                } else {
                                    $results[$functionName][$documentType] = [$results[$functionName][$documentType], $documentString];
                                }
                            } else {
                                $results[$functionName][$documentType] = $documentString;
                            }
                        }
                    }
                }
            }

        }

        return $results;
    }

}