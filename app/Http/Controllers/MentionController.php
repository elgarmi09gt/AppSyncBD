<?php

namespace App\Http\Controllers;


use App\Models\Scol\Mention as ScolMention;
use App\Models\Ref\Mention;
use App\Models\Scol\Domaine as ScolDomaine;
use App\Models\Ref\Domaine;
use Illuminate\Support\Facades\DB;
use Exception;

class MentionController extends Controller
{
    public function syncMention()
    {
        //Mentions(Scolarité) : Scol_mention
        $scol_mentions = ScolMention::all();
        foreach ($scol_mentions as $scol_mention) {
            //Domaine(Scolarité) : Scol_domaine
            $scol_domaine = ScolDomaine::where('code_domaine', $scol_mention->code_domaine)
                ->first();
            if ($scol_domaine) {
                //Domaine(Refonte)
                $domaine = Domaine::where('libelle_long', $scol_domaine->lib_long_domaine)
                    ->where('libelle_court', $scol_domaine->lib_court_domaine)
                    ->first();
                if ($domaine) {
                    $mention = Mention::where('libelle_long', $scol_mention->lib_long_mention)
                        ->where('libelle_court', $scol_mention->lib_court_mention)
                        ->where('id_domaine', $domaine->id_domaine)
                        ->first();
                    if (!$mention) {
                        try {
                            DB::connection('oracle')->table('mentions')->insert([
                                'libelle_long' => $scol_mention->lib_long_mention,
                                'libelle_court' => $scol_mention->lib_court_mention,
                                'id_domaine' => $domaine->id_domaine,
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
        return back()->with('success', 'Synchronisation Mentions effectué');
    }
}
