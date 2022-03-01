<?php
class GLHttpRequests extends GLibrary
{
    public function validate_jwt_token()
    {
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $headers['Authorization'] =
                $headers['Authorization'] ?? $headers['authorization'];
        } else {
            $headers['Authorization'] =
                'Bearer ' . $_SERVER['HTTP_AUTHORIZATION'];
        }
        $response = [];
        // Verifying Authorization Header
        if (isset($headers['Authorization'])) {
            $token = substr($headers['Authorization'], 7);
            $this->load->library('helpers/encrypt');
            $verify = $this->library_helpers_encrypt->verify_jwt($token);
            if (!$verify['status']) {
                $this->emit($verify);
            } else {
                return $verify['data'];
            }
        } else {
            // api key is missing in header
            $response['message'] = 'Api key is misssing';
            $response['code'] = 401;
            $this->emit($response);
        }
    }

    public function sanitize($var)
    {
        // $var = $this->dbconn->real_escape_string($var);
        $var = htmlentities(strip_tags(stripslashes($var)));
        return $var;
    }

    public function emit($res, $code = 200)
    {
        // $keys = array_keys($res);
        if (!isset($res['status']) || !$res['status']) {
            $res['error'] = true;
            $code =
                $code !== 200
                    ? $code
                    : (isset($res['code'])
                        ? $res['code']
                        : 400);
            unset($res['status']);
        }
        header('Access-Control-Allow-Methods: POST, GET,OPTIONS');
        header(
            'Access-Control-Allow-Headers: Access-Control-Allow-Origin,Access-Control-Allow-Methods,Content-Type,AUTHORIZATION,authorization'
        );
        header('Content-Type: application/json;charset=UTF-8');
        $code = $_SERVER['REQUEST_METHOD'] === 'OPTIONS' ? 200 : $code;
        // The request is using the POST method
        header('Access-Control-Allow-Origin:   *', true, $code);
        // header(':',true,$code);
        // http_response_code ($code);
        exit(json_encode($res));
    }

    public function emitfile($filepath, $mimeType)
    {
        foreach (HEADERS as $item => $value) {
            header("$item : $value ");
        }
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimeType);
        header(
            "Content-Disposition: attachment; filename=\"" .
                basename($filepath) .
                "\";"
        );
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        ob_clean();
        flush();
        readfile(BASE_PATH . $filepath); //showing the path to the server where the file is to be download
        exit();
    }

    public function post($fields, $JSON = false)
    {
        if ($JSON) {
            return $this->JSONPost($fields);
        }
        if (gettype($fields) == 'array') {
            $postData = [];
            for ($i = 0; $i < sizeof($fields); $i++) {
                $postData[$fields[$i]] = isset($_POST[$fields[$i]])
                    ? $this->deepSanitize($_POST[$fields[$i]])
                    : null;
            }
            return $postData;
        } else {
            return isset($_POST[$fields])
                ? $this->deepSanitize($_POST[$fields])
                : null;
        }
    }

    public function get($fields)
    {
        if (gettype($fields) == 'array') {
            $postData = [];
            for ($i = 0; $i < sizeof($fields); $i++) {
                $postData[$fields[$i]] = isset($_GET[$fields[$i]])
                    ? $this->deepSanitize($_GET[$fields[$i]])
                    : null;
            }
            return $postData;
        } else {
            return isset($_GET[$fields])
                ? $this->deepSanitize($_GET[$fields])
                : null;
        }
    }

    public function JSONPost($fields)
    {
        $post = json_decode(file_get_contents('php://input'), true);
        if (gettype($fields) == 'array') {
            $postData = [];
            foreach ($fields as $item) {
                if (isset($post[$item])) {
                    $postData[$item] = isset($post[$item])
                        ? $this->deepSanitize($post[$item])
                        : null;
                }
            }
            return $postData;
        } else {
            return isset($post[$item])
                ? $this->deepSanitize($post[$item])
                : null;
        }
    }

    protected function deepSanitize($data)
    {
        $sanitized = [];
        if (!is_array($data)) {
            return $this->sanitize($data);
        }
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                array_merge($sanitized, [$key => deepSanitize($value)]);
            } else {
                array_merge($sanitized, [$key => $this->sanitize($value)]);
            }
        }
        return $sanitized;
    }

    public function do_post($url, $params = [], $headers = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $params_string = '';
        if (is_array($params) && count($params)) {
            foreach ($params as $key => $value) {
                $params_string .= $key . '=' . $value . '&';
            }
            rtrim($params_string, '&');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
            curl_setopt($ch, CURLOPT_POST, count($params));
        }
        curl_setopt($ch, CURLOPT_HEADER, $headers);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }

    public function do_get($url, $params = [], $headers = [])
    {
        $ch = curl_init();

        $params_string = '';
        if (is_array($params) && count($params)) {
            foreach ($params as $key => $value) {
                $params_string .= $key . '=' . $value . '&';
            }
            rtrim($params_string, '&');
        }
        curl_setopt($ch, CURLOPT_URL, $url . $params_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, $headers);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }
}
?>
