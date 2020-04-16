<?php

namespace App\Http\Controllers;

use App\Movies;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $year = $request->input('yil');
        $genres = $request->input('tur');
        $film=$request->input('ara');



        if($year) {
            $response = $this->getYearData($year);
        }else if($film){
           $response= $this->getSearchData($film);
        }else if($genres){
            $response= $this->getGenresData($genres);
        }else{
            $response=$this->getDataTop10();
        }

        return response()->json($response,$response['status']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
   //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getSearchData($text)
    {
        $movies = Movies::where('title','LIKE','%'.$text.'%')->get();

        if($movies->count()===0){

           $client =new \GuzzleHttp\Client();
            $omdresponse = $client->request('GET', "http://www.omdbapi.com/?apikey=5e31c00a&t=$text", [
                'form_params' => [

                ]
            ]);
            $omdresponse = $omdresponse->getBody()->getContents();

            $response = [
                'success' => false,
                'messsage'    => 'Aranan film sistemde bulunamadı. OMDB Api Sisteminden getirildi.',
                'status'    => 404,
                'data'    => json_decode($omdresponse),

            ];
        }else{
            $response = [
                'success' => true,
                'messsage'    => 'Arama sonucunda sistemde '.$movies->count().' film bulundu.',
                'status'    => 200,
                'data'    => $movies,
            ];
        }
       return $response;
    }

    public function getYearData($text)
    {
         $movies = Movies::where('title','LIKE','%'.$text.'%')->get();

        if($movies->count()===0){
            $response = [
                'success' => false,
                'messsage'    => 'Aranan '.$text.' yılına ait film sistemde bulunamadı',
                'status'    => 404,
                'data'    => $movies,

            ];
        }else{
            $response = [
                'success' => true,
                'messsage'    => 'Arama sonucunda sistemde '.$movies->count().' film bulundu.',
                'status'    => 200,
                'data'    => $movies,
            ];

        }
        return $response;
    }

    public  function  getGenresData($text)
    {
        $genres_arr=explode(',',$text);

        $movies=Movies::where(function ($q) use ($genres_arr){
            foreach ($genres_arr as $value) {
                if($value != null ){
                    $q->where('genres', 'like', "%{$value}%");
                }
            }
        })->get();

        if($movies->count()===0){
            $response = [
                'success' => false,
                'messsage'    => 'Aranan film sistemde bulunamadı',
                'status'    => 404,
                'data'    => $movies,
            ];
        }else{
            $response = [
                'success' => true,
                'messsage'    => 'Arama sonucunda sistemde '.$movies->count().' film bulundu.',
                'status'    => 200,
                'data'    => $movies,
            ];
        }
        return $response;

    }

    public  function  getDataTop10()
    {
        $movies = Movies::all()->take('10');
        $response = [
            'success' => true,
            'messsage'    => 'Arama sonucunda sistemde '.$movies->count().' film bulundu.',
            'status'    => 200,
            'data'    => $movies,
        ];
        return $response;
    }



}
