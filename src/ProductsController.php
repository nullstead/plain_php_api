<?php 

class ProductsController{ 

    //using ProductGateway class as a dependency instead of creating its object
     //---------------------------start-----------------------------------
    public function __construct(private ProductGateway $gateway){
        
    }
     //---------------------------end-----------------------------------





    //---------------------------start-----------------------------------
    public function processRequest(string $method, ?string $id): void {
        if($id){
            //return single
            $this->processResourceRequest($method, $id);
        }else {
            //return collection
            $this->processCollectionRequest($method);
        }
    }
     //---------------------------end--------------------------------------





      //---------------------------start-----------------------------------
    //Processing for id-specific Requests
    private function processResourceRequest(string $method, ?string $id) : void {

        //get product if exists
        $product = $this->gateway->getOne($id);

        if(! $product){
            http_response_code(404);
            echo json_encode([
                "message" => "Product not found"
            ]);

            return;
        }

        //switch request method once product exists
        switch($method){
            case "GET": 
                echo json_encode($product);
                break;

            case "PATCH":
                //getting JSON datafrom PATCH request
                $data = file_get_contents("php://input");
                $data = (array) json_decode($data, true);

                //call validate input
                $errors = $this->getValidateErrors($data, false);

                if(!empty($errors)){  
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                } else {
                    $rows   = $this->gateway->update($product, $data);
                    
                    echo json_encode([
                        "message" => "Product $id updated succefully",
                        "rows"      => $rows
                    ]);
                    break;
                }

            case "DELETE":
                $rows = $this->gateway->delete($id);
                
                echo json_encode([
                    "message" => "Product $id deleted !",
                    "rows" => $rows
                ]);
                break;

            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }
    
    }
     //---------------------------end-----------------------------------





      //---------------------------start-----------------------------------
    //Processing for Collection Requests
    private function processCollectionRequest(string $method) : void {
        switch($method){
            case "GET":
                echo json_encode($this->gateway->getAll());
                break;
            case "POST":
                //getting JSON datafrom POST request
                $data = file_get_contents("php://input");
                $data = (array) json_decode($data, true);

                //call validate input
                $errors = $this->getValidateErrors($data);
                    if(!empty($errors)){  
                        http_response_code(422);
                        echo json_encode(["errors" => $errors]);
                        break;

                    } else {
                        $id   = $this->gateway->create($data);
                        http_response_code(201);
                        echo json_encode([
                            "message" => "Product created succefully",
                            "id"      => $id
                        ]);
                        break;
                    }  
                
                default:
                    http_response_code(405);
                    header("Allow: GET, POST");
        }//end switch
    }
     //---------------------------end-----------------------------------





    //---------------------------start-----------------------------------
    //validate inputs
    private function getValidateErrors(array $data, bool $is_new = true) : array {
        $errors = [];

        if($is_new && empty($data["name"])){
            $errors[] = "name is required";
        }

        if(array_key_exists("size", $data)){
            if(filter_var($data["size"], FILTER_VALIDATE_INT) === false){
                $errors[] = "size must be an integer";
            }
        }

        return $errors;
    }
     //---------------------------end-----------------------------------


} 