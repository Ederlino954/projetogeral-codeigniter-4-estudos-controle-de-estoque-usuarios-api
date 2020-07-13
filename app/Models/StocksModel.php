<?php namespace App\Models;

use CodeIgniter\Model;

class StocksModel extends Model{

    protected $db;

    // ===========================================
    // familias
    // ===========================================
    public function __construct(){
        $this->db = db_connect();
    }

    // ===========================================
    public function get_all_families(){

        // returns all families
        // return $this->query("SELECT * FROM stock_familias")->getResult('array');
        return $this->query('

        SELECT a.*, b.designacao AS parent 
        FROM stock_familias a LEFT JOIN stock_familias b
        ON a.id_parent = b.id_familia

        ')->getResult('array'); // selecionando familias e parent // tabela a proridade seguindo a tabela b quando tiver item 
    }

    // ===========================================
    public function get_family($id_family){

        // returns the family
        $params = array($id_family);
        $results = $this->query("SELECT * FROM stock_familias WHERE id_familia = ?", $params)->getResult('array');
        
        if(count($results) == 1){
            return $results[0]; // devolvendo a linha 0
        } else {
            return array();
        }
    }

    // ===========================================
    public function check_family($designacao){ // checando a repetição da familia

        $params = array( // parametrização para evitar o injection 
            $designacao
        );

        $results = $this->query("SELECT * FROM stock_familias WHERE designacao = ?", $params)->getResult('array');
        if(count($results) != 0){
            return true;
        } else {
            return false;
        }        
    }

    // ===========================================
    public function family_add(){

        // adicionar uma nova família de produtos à base de dados
        $request = \Config\Services::request();
        $params = array(
            $request->getPost('select_parent'),
            $request->getPost('text_designacao')
        );

        $this->query("INSERT INTO stock_familias VALUES(0, ?, ?, '')", $params);
    }

    // ===========================================
    public function check_other_family($designacao, $id_family){

        $params = array(
            $designacao,
            $id_family
        );

        $results = $this->query("SELECT * FROM stock_familias WHERE designacao = ? AND id_familia <> ?", $params)->getResult('array'); // familias diferentes 
        if(count($results) != 0){
            return true;
        } else {
            return false;
        }        
    }

    // ===========================================
    public function family_edit($id_family){

        // editar os dados da família
        $request = \Config\Services::request();
        $params = array(
            $request->getPost('select_parent'),
            $request->getPost('text_designacao'),
            $id_family
        );

        $this->query(
            "UPDATE stock_familias ".
            "SET id_parent = ?, ".
            "designacao = ? ".
            "WHERE id_familia = ?",
            $params);
    }

    // ===========================================
    public function delete_family($id_family){ /// elimina a familia alterando o id_parent para 0 das ligações se possuir 

        // eliminar a família e alterar o id dos parents
        $params = array(
            $id_family
        );

        // delete the selected family
        $this->query("DELETE FROM stock_familias WHERE id_familia = ?", $params);

        // updates all the families where id_parent is id_family // 
        $this->query("UPDATE stock_familias SET id_parent = 0 WHERE id_parent = ?", $params);
    }





    // ===========================================
    // taxas
    // ===========================================
    public function get_all_taxes(){

        // returns all taxes
        return $this->query("SELECT * FROM stock_taxas")->getResult('array');
    }

    // ===========================================
    public function check_tax($designacao){

        // check if there is a taxe with the same name
        $params = array(
            $designacao
        );

        $results = $this->query("SELECT * FROM stock_taxas WHERE designacao = ?", $params)->getResult('array');
        if(count($results) != 0){
            return true; // já existe 
        } else {
            return false; // não existe 
        }        
    }

    // ===========================================
    public function tax_add(){

        // adicionar uma nova taxa à base de dados
        $request = \Config\Services::request();
        $params = array(
            $request->getPost('text_designacao'),
            $request->getPost('text_valor')
        );

        $this->query("INSERT INTO stock_taxas VALUES(0, ?, ?)", $params);
    }

    // ===========================================
    public function get_tax($id_taxa){

        // vai buscar uma taxa específica conforme id 
        $params = array(
            $id_taxa
        );
        return $this->query("SELECT * FROM stock_taxas WHERE id_taxa = ?", $params)->getResult('array')[0];
    }

    // ===========================================
    public function check_other_tax($designacao, $id_taxa){ // verifica se já existe taxa com o mesmo nome e id diferente 

        $params = array(
            $designacao,
            $id_taxa
        );

        $results = $this->query("SELECT * FROM stock_taxas WHERE designacao = ? AND id_taxa <> ?", $params)->getResult('array');
        if(count($results) != 0){
            return true; // já existe 
        } else {
            return false; // não existe 
        }        
    }

    // ===========================================
    public function tax_edit($id_taxa){

        // editar os dados da taxa
        $request = \Config\Services::request();
        $params = array(            
            $request->getPost('text_designacao'),
            $request->getPost('text_valor'),
            $id_taxa
        );

        $this->query(
            "UPDATE stock_taxas ".
            "SET designacao = ?, ".
            "percentagem = ? ".
            "WHERE id_taxa = ?",
            $params);
    }

    // ===========================================
    public function delete_tax($id_taxa){

        // eliminar a taxa e alterar o id nos produtos
        $params = array(
            $id_taxa
        );

        // delete the selected tax
        $this->query("DELETE FROM stock_taxas WHERE id_taxa = ?", $params);

        // updates all the products where id_taxa is id_taxa // quando relacionada com o produto 
        $this->query("UPDATE stock_produtos SET id_taxa = 0 WHERE id_taxa = ?", $params);
    }




    // ===========================================
    // produtos
    // ===========================================
    public function get_all_products(){

        // returns all products // mesmo com os campos vazios 
        return $this->query(
            "SELECT " .
                "p.id_produto, p.designacao AS nome_produto, p.preco, p.quantidade, " .
                "f.designacao AS familia, " .
                "t.designacao AS taxa, t.percentagem " .
            "FROM stock_produtos p ".
            "LEFT JOIN stock_familias f ON p.id_familia = f.id_familia " .
            "LEFT JOIN stock_taxas t ON p.id_taxa = t.id_taxa"
        )->getResult('array');
    }

    // ===========================================
    public function get_product($id){

        // returns a specific product
        $params = array( $id );
        return $this->query(
            "SELECT * FROM stock_produtos WHERE id_produto = ?", $params
        )->getResult('array')[0]; // retorna apenas uma linha 
    }

    // ===========================================
    public function product_check(){
        
        // verifica se já existe um produto com o mesmo nome
        $request = \Config\Services::request();
        $params = array(
            $request->getPost('text_designacao')
        );

        $results = $this->query("SELECT designacao FROM stock_produtos WHERE designacao = ?", $params)->getResult('array');
        if(count($results) != 0){
            return true;
        } else {
            return false;
        } 
    }

    // ===========================================
    public function product_other_check($id_produto, $designacao){
        
        // verifica se já outro produto com o mesmo nome
        $request = \Config\Services::request();
        $params = array(
            $designacao,
            $id_produto
        );

        $results = $this->query("SELECT designacao FROM stock_produtos WHERE designacao = ? AND id_produto <> ?", $params)->getResult('array');
        if(count($results) != 0){
            return true;
        } else {
            return false;
        } 
    }

    // ===========================================
    public function product_add($nome_ficheiro){ /// parametro

        // adicionar um novo produto à base de dados
        $request = \Config\Services::request();
        $params = array(
            $request->getPost('combo_familia'),
            $request->getPost('text_designacao'),
            $request->getPost('text_descricao'),
            $nome_ficheiro, /// parâmetro
            $request->getPost('text_preco'),
            $request->getPost('combo_taxa'),
            $request->getPost('text_quantidade'),
            $request->getPost('text_detalhes'),
        );

        $this->query(
            "
                INSERT INTO stock_produtos VALUES(
                    0,
                    ?, ?, ?, ?, ?, ?, ?, ?,
                    NOW()
                )
            ", $params);        
    }

    // ===========================================
    public function product_edit($id_produto, $imagem = ''){

        $request = \Config\Services::request();  // buscando as informações dos campos 

        // atualizar o produto sem nova imagem
        if($imagem == ''){
            $params = array(
                $request->getPost('combo_familia'),
                $request->getPost('text_designacao'),
                $request->getPost('text_descricao'),
                $request->getPost('text_preco'),
                $request->getPost('combo_taxa'),
                $request->getPost('text_quantidade'),
                $request->getPost('text_detalhes'),
                $id_produto
            );

            $this->query(
                "UPDATE stock_produtos SET ".
                "id_familia = ?, ".
                "designacao = ?, ".
                "descricao = ?, ".
                "preco = ?, ".
                "id_taxa = ?, ".
                "quantidade = ?, ".
                "detalhes = ?, ".
                "atualizacao = NOW() ".
                "WHERE id_produto = ?", $params
            );
        } 
        
        // atualizar o produto com nova imagem
        if($imagem != ''){
            $params = array(
                $request->getPost('combo_familia'),
                $request->getPost('text_designacao'),
                $request->getPost('text_descricao'),
                $imagem, 
                $request->getPost('text_preco'),
                $request->getPost('combo_taxa'),
                $request->getPost('text_quantidade'),
                $request->getPost('text_detalhes'),
                $id_produto
            );

            $this->query(
                "UPDATE stock_produtos SET ".
                "id_familia = ?, ".
                "designacao = ?, ".
                "descricao = ?, ".
                "imagem = ?, ".
                "preco = ?, ".
                "id_taxa = ?, ".
                "quantidade = ?, ".
                "detalhes = ?, ".
                "atualizacao = NOW() ".
                "WHERE id_produto = ?", $params
            );
        } 
    }

    // ===========================================
    public function delete_product($id_produto){

        // eliminar o produto
        $params = array(
            $id_produto
        );

        // delete the selected product
        $this->query("DELETE FROM stock_produtos WHERE id_produto = ?", $params);        
    }


    // ===========================================
    // MOVIMENTOS
    // ===========================================
    public function get_movimentos(){
        return $this->query("
            SELECT m.*, p.designacao 
            FROM stock_movimentos m, stock_produtos p
            WHERE p.id_produto = m.id_produto 
            ORDER BY m.data_movimento DESC
        "
        )->getResult('array');
    }


}

