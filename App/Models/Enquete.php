<?php

namespace App\Models;

use MF\Model\Model;
use Spatie\Dropbox\Client as DropboxClient;

class Enquete extends Model
{
    private $id;
    private $idTurma;
    private $idEnquete;
    private $idEnquetePergunta;
    private $idEnqueteResposta;
    private $titulo;
    private $dataInicial;
    private $dataFinal;
    private $descricao;
    private $opcao;

    public function __get($atributo)
    {
        return $this->$atributo;
    }
    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }
    public function criar()
    {

        $query = "INSERT INTO enquete (id_turma, titulo, texto, data_inicial, data_final) 
        VALUES (:idTurma, :titulo, :descricao, :dataInicial, :dataFinal)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idTurma', $this->__get('idTurma'));
        $stmt->bindValue(':titulo', $this->__get('titulo'));
        $stmt->bindValue(':descricao', $this->__get('descricao'));
        $stmt->bindValue(':dataInicial', $this->__get('dataInicial'));
        $stmt->bindValue(':dataFinal', $this->__get('dataFinal'));
        if ($stmt->execute()) {
            $query = "SELECT * FROM enquete WHERE id_turma = :idTurma AND titulo = :titulo AND texto = :descricao";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':idTurma', $this->__get('idTurma'));
            $stmt->bindValue(':titulo', $this->__get('titulo'));
            $stmt->bindValue(':descricao', $this->__get('descricao'));
            if ($stmt->execute()) {
                $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $opcaoCriadas = json_decode($this->opcao);
                $countQuery = 1;
                $query = "INSERT INTO enquete_pergunta (idEnquete, idDono, pergunta) VALUES ";
                foreach ($opcaoCriadas as $valor_opcao) {
                    if ($countQuery == count($opcaoCriadas)) {
                        $query .= "({$resultado[0]['id']}, $this->id, '{$valor_opcao->opcao}');";
                    } else {
                        $query .= "({$resultado[0]['id']}, $this->id, '{$valor_opcao->opcao}'), ";
                    }
                    $countQuery++;
                }
                $stmt = $this->db->prepare($query);
                if ($stmt->execute()) {
                    return true;
                }
            }
        }

        return false;
    }
    public function mostrar()
    {
        /*$query = "SELECT 
        enquete.titulo, 
        enquete.texto, 
        enquete_pergunta.pergunta, 
        FROM enquete
        INNER JOIN enquete_pergunta ON enquete.id_turma=enquete_pergunta.id 
        WHERE enquete.id_turma = :idTurma ORDER BY enquete.id DESC";*/
        $query = "SELECT * FROM enquete WHERE id_turma = :idTurma AND id = :idEnquete ORDER BY id DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idTurma', $this->__get('idTurma'));
        $stmt->bindValue(':idEnquete', $this->__get('idEnquete'));
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $resultado;
        }
        return [];
    }
    public function mostrarInformacoesGrafico()
    {
        /*$query = "SELECT 
        enquete.titulo, 
        enquete.texto, 
        enquete_pergunta.pergunta, 
        FROM enquete
        INNER JOIN enquete_pergunta ON enquete.id_turma=enquete_pergunta.id 
        WHERE enquete.id_turma = :idTurma ORDER BY enquete.id DESC";*/

        $query = "SELECT * FROM enquete WHERE id_turma = :idTurma ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idTurma', $this->__get('idTurma'));
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $resultado;
        }
        return false;
    }
    public function mostrarUsuarioVotador()
    {
        //TOKEN DE ACESSO
        $envPath = realpath(dirname(__FILE__) . '/../../env.ini');
        $env = parse_ini_file($envPath);
        $token = $env['tokendropbox'];
        //INSTANCIA DO CLIENTE DROPBOX
        $obDropboxClient = new DropboxClient($token);

        $query = "SELECT 
        enquete_resposta.id, 
        enquete_resposta.idEnqueteResposta, 
        enquete_resposta.idEnquete, 
        enquete_resposta.idDono, 
        usuario.id, 
        usuario.nome, 
        usuario.foto 
        FROM enquete_resposta
        INNER JOIN usuario ON enquete_resposta.idDono=usuario.id
        WHERE enquete_resposta.id = :idEnqueteResposta ORDER BY enquete_resposta.id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idEnqueteResposta', $this->__get('idEnqueteResposta'));
        $content = [];
        if ($stmt->execute()) {
            $value = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $value = $value[0];

            $link = 'perfil.webp';

            //$list = $obDropboxClient->listFolder('/Gandula');
            if ($value['foto'] == 'perfil.webp') {
                $link = 'perfil.webp';
            } else {
                $link = $obDropboxClient->getTemporaryLink('/Gandula/' . $value['foto'] . '');
            }

            $content = [
                "id" => $value['id'],
                "idEnqueteResposta" => $value['idEnqueteResposta'],
                "idEnquete" => $value['idEnquete'],
                "idDono" => $value['idDono'],
                "nome" => $value['nome'],
                "foto" =>  $link,
            ];

            return $content;
        }
        return [];
    }

    public function mostrarPerguntas($idEnquete)
    {
        $query = "SELECT * FROM enquete_pergunta WHERE idEnquete = :id ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $idEnquete);
        $stmt->execute();
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $resultado;
        }
        return false;
    }
    public function mostrarPerguntasInformacoesGrafico()
    {
        $query = "SELECT * FROM enquete_pergunta ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $resultado;
        }
        return false;
    }
    public function mostrarRespostaInformacoesGrafico()
    {
        $query = "SELECT * FROM enquete_resposta ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $resultado;
        }
        return false;
    }

    public function mostrarEnquete()
    {
        $query = "SELECT * FROM enquete WHERE id_turma = :idTurma ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idTurma', $this->__get('idTurma'));
        $stmt->execute();
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $resultado;
        }
        return [];
    }
    public function salvarVoto()
    {
        if ($this->getUserTableEnqueteResposta($this->__get('idEnquete'), $this->__get('id'))) {
            return false;
        }
        $query = "INSERT INTO enquete_resposta (idEnqueteResposta, idEnquete, idDono) 
        VALUES (:idEnquetePergunta, :idEnquete, :id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idEnquetePergunta', $this->__get('idEnquetePergunta'));
        $stmt->bindValue(':idEnquete', $this->__get('idEnquete'));
        $stmt->bindValue(':id', $this->__get('id'));
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    //verificar se o  usuário já votou
    public function getUserTableEnqueteResposta($idEnquete, $idUsuario)
    {
        $query = "SELECT * FROM enquete_resposta WHERE idEnquete = :idEnquete AND idDono = :idDono";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idEnquete', $idEnquete);
        $stmt->bindValue(':idDono', $idUsuario);
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $resultado;
        }
        return false;
    }
}
