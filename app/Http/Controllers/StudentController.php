<?php

namespace App\Http\Controllers;

use App\Student;
use Illuminate\Http\Request;

//Importação da Request
use App\Http\Requests\CreateStudent;

//Importação de métodos de Storage (no caso, da foto de perfil)
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = Student::all();

        return $students->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateStudent $request)
    {
        $newStudent = new Student;

        $newStudent->nome = $request->nome;
        $newStudent->idade = $request->idade;
        $newStudent->email = $request->email;
        $newStudent->cpf = $request->cpf;
        $newStudent->telefone = $request->telefone;

        if (!Storage::exists('localDocuments/'))
        {
            Storage::makeDirectory('localDocuments/', 0775, true);
        }

        $document = base64_decode($request->boletim);
        $docName = uniqid().'.pdf';
        $path = storage_path('/app/localDocuments/'.$docName);
        file_put_contents($path,$document);
        $newStudent->boletim = $docName;

        $newStudent->save();
        return response()->json('Estudante criado com sucesso!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        return response()->json('Estudante: '.$student);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(CreateStudent $request, Student $student)
    {
        if($request->boletim){
            Storage::delete('localDocuments/'.$student->boletim);
            if (!Storage::exists('localDocuments/'))
            {
                Storage::makeDirectory('localDocuments/', 0775, true);
            }

            $document = base64_decode($request->boletim);
            $docName = uniqid().'.pdf';
            $path = storage_path('/app/localDocuments/'.$docName);
            file_put_contents($path,$document);
            $newStudent->boletim = $docName;

            $newStudent->save();
            return response()->json('Estudante criado com sucesso!');
        }
        if($request->nome){
          $student->nome = $request->nome;
        }
        if($request->idade){
          $student->idade = $request->idade;
        }
        if($request->email){
          $student->email = $request->email;
        }
        if($request->cpf){
          $student->cpf = $request->cpf;
        }
        if($request->telefone){
          $student->telefone = $request->telefone;
        }

        $student->save();
        return response()->json('Estudante atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        Storage::delete('localDocuments/'.$student->doc);
        Student::destroy($student->id);
        return response()->json('Estudante deletado com sucesso!');
    }
}
