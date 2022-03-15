<?php
// namespace GREY\GCore;
class GCLoader
{
    private $libraries;
    private $class;

    public function db()
    {
        try {
            $db = $this->load_default_library('db/' . DB_ENGINE);
            $this->db = new $db();
            $this->db->connect();
        } catch (\Throwable $th) {
            throw new GCDBError($th);
        }
    }

    public function setClass($class)
    {
        $this->class = $class;
    }

    private function extractUrl($type, $path)
    {
        $route = '';
        if (strpos($path, '/') > -1) {
            $classname = implode('', array_map('ucwords', explode('/', $path)));
            $file = substr($path, strrpos($path, '/') + 1);
            $route = substr($path, 0, strrpos($path, '/') + 1);
        } else {
            $classname = $file = $path;
        }
        switch ($type) {
            case 'library':
                $url =
                    BASE_PATH .
                    APP_ROOT_DIR .
                    '/GSystem/GLibraries/' .
                    $route .
                    'gl_' .
                    $file .
                    '.php';
                $name = 'library_' . str_replace('/', '_', $route) . $file;
                break;
            case 'model':
                $url =
                    BASE_PATH .
                    APP_ROOT_DIR .
                    '/src/GModels/' .
                    $route .
                    'gm_' .
                    $file .
                    '.php';
                $name = 'model_' . str_replace('/', '_', $route) . $file;
                break;
            case 'controller':
                $url =
                    BASE_PATH .
                    APP_ROOT_DIR .
                    '/src/GControllers/' .
                    $route .
                    'gc_' .
                    $file .
                    '.php';
                $name = 'controller_' . str_replace('/', '_', $route) . $file;
                break;
            case 'route':
                $url =
                    BASE_PATH .
                    APP_ROOT_DIR .
                    '/src/GRoutes/' .
                    $route .
                    'gr_' .
                    $file .
                    '.php';
                $name = 'route_' . str_replace('/', '_', $route) . $file;
                break;
            case 'helper':
                $url =
                    BASE_PATH .
                    APP_ROOT_DIR .
                    '/src/GHelpers/' .
                    $route .
                    'gh_' .
                    $file .
                    '.php';
                $name = 'route_' . str_replace('/', '_', $route) . $file;
                break;
            default:
                # code...
                break;
        }

        if (file_exists($url)) {
            include_once $url;
            return ['class' => $classname, 'name' => $name];
        } else {
            throw new Exception(
                "Error: Loading $type  $path. File does not exist"
            );
        }
    }

    public function library($name, $options = ['use_db' => false])
    {
        try {
            $library = $this->extractUrl('library', $name);
            $className = 'GL' . ucwords($library['class']);
            $name = $library['name'];
            if (class_exists($className)) {
                $this->class->$name = new $className();
                LOAD_DATABASE &&
                    $options['use_db'] &&
                    ($this->class->$name->db = $this->db);
                return $this->class->$name;
            } else {
                throw new Exception(
                    "Error: In-built libraries matching \"$name\". Class does not exist"
                );
            }
        } catch (\Throwable $th) {
            throw new GCLoaderError($th);
        }
    }

    public function load_default_library($name)
    {
        try {
            $library = $this->extractUrl('library', $name);
            $className = 'GL' . ucwords($library['class']);
            $name = $library['name'];
            if (class_exists($className)) {
                return new $className();
            } else {
                throw new Exception(
                    "Error: In-built libraries matching \"$name\". Class does not exist"
                );
            }
        } catch (\Throwable $th) {
            throw new GCLoaderError($th);
        }
    }

    public function model($name)
    {
        try {
            $model = $this->extractUrl('model', $name);

            $className = 'GM' . ucwords($model['class']);
            $modelName = $model['name'];
            if (class_exists($className)) {
                $this->class->$modelName = new $className();
                $this->class->$modelName->db = $this->db;
                return $this->class->$modelName;
            } else {
                throw new Exception(
                    "Error loading model $name. Class does not exist"
                );
            }
        } catch (\Throwable $th) {
            throw new GCLoaderError($th);
        }
    }

    public function controller($name)
    {
        try {
            $controller = $this->extractUrl('controller', $name);
            $className = 'GC' . ucwords($controller['class']);
            if (class_exists($className)) {
                $name = $controller['name'];
                $this->class = new $className();
                return $this->class->$name = $this->load_library_on_class(
                    $this->class
                );
            } else {
                throw new Exception(
                    "Error loading controller $name. Class does not exist"
                );
            }
        } catch (\Throwable $th) {
            throw new GCRouteError($th);
        }
    }

    public function route($route, $method)
    {
        try {
            $class = $this->controller($route);
            if (method_exists($class, $method)) {
                $class->$method();
            } elseif (method_exists($class, 'index') && strlen($method) < 1) {
                $class->index();
            } else {
                throw new GCRouteError(
                    new Exception(
                        "Error: Attempt to access route $route->$method. Method $method does not exist on class " .
                            get_class($class) .
                            '.'
                    )
                );
            }
        } catch (\Throwable $th) {
            new GCHandler($th);
            if ($th instanceof GCRouteError) {
                $this->libraries['request']->emit([
                    'status' => false,
                    'message' => 'The requested resource does not exist',
                    'code' => 404,
                ]);
            }
            $this->libraries['request']->emit([
                'status' => false,
                'message' => 'unlucky',
                'code' => 500,
            ]);
        }
    }

    public function helper($path, $function, $params = [])
    {
        try {
            $helper = $this->extractUrl('helper', $path);
            $className = 'GH' . ucwords($helper['class']);
            if (class_exists($className)) {
                $name = $helper['name'];
                $this->class->$name = $this->load_library_on_class(
                    new $className()
                );
                if (method_exists($this->class->$name, $function)) {
                    return $this->class->$name->$function($params);
                } else {
                    throw new Exception(
                        "Error loading helper $name. Method does not exist"
                    );
                }
            } else {
                throw new Exception(
                    "Error loading helper $path. Class does not exist"
                );
            }
        } catch (\Throwable $th) {
            throw new GCHelperError($th);
        }
    }

    public function public($file)
    {
        $this->libraries['request']->emitfile(
            $file,
            $this->libraries['file']->getFileMimeType($file)
        );
    }

    public function preloadlibrary()
    {
        global $default_libraries;
        foreach ($default_libraries as $key => $value) {
            $this->libraries[$key] = $this->load_default_library($value);
        }
    }

    private function load_library_on_class($class)
    {
        global $default_libraries;
        foreach ($default_libraries as $key => $value) {
            $class->$key = $this->libraries[$key];
        }
        return $class;
    }
}
?>
