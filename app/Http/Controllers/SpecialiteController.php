<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\Ref\Mention;
use App\Models\Scol\Mention as ScolMention;
use App\Models\Ref\Specialite;
use App\Models\Scol\Specialite as ScolSpecialite;

class SpecialiteController extends Controller
{
    public function syncSpecialite()
    {
        $scolSpecialites = ScolSpecialite::all();
        foreach ($scolSpecialites as $scolSpecialite) {
            $scolMention = ScolMention::where('code_mention', $scolSpecialite->code_mention)->first();
            if ($scolMention) {
                $mention = Mention::where('libelle_long', $scolMention->lib_long_mention)
                    ->where('libelle_court', $scolMention->lib_court_mention)
                    ->first();
                if ($mention) {
                    $specialite = Specialite::where('libelle_long', $scolSpecialite->lib_specialite)
                        ->where('libelle_court', $scolSpecialite->lib_court_specialite)
                        ->where('id_mention', $mention->id_mention)
                        ->first();
                    if (!$specialite) {
                        try {
                            DB::connection('oracle')->table('specialites')->insert([
                                'libelle_long' => $scolSpecialite->lib_specialite,
                                'libelle_court' => $scolSpecialite->lib_court_specialite,
                                'id_mention' => $mention->id_mention,
                                'uti_cree' => 33
                            ]);
                        } catch (Exception $e) {
                            dump($e->getMessage());
                        }
                    }
                }
            }
        }
        session_commit();
        return back()->with('success', 'Synchronisation Spécialités effectué');
    }
}
