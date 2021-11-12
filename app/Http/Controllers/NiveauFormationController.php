<?php

namespace App\Http\Controllers;


use App\Models\Ref\NiveauxFormation;
use App\Models\Ref\Formation;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;
use App\Models\Scol\Departement as ScolDepartement;
use App\Models\Ref\Departement;
use App\Models\Ref\Mention;
use App\Models\Scol\Mention as ScolMention;
use App\Models\Ref\GradeFormation;
use App\Models\Scol\GradeFormation as ScolGradeFormation;
use App\Models\Ref\Cycle;
use App\Models\Scol\Cycle as ScolCycle;
use App\Models\Ref\TypeFormation;
use App\Models\Scol\TypeFormation as ScolTypeFormation;
use App\Models\Ref\Etablissement;
use App\Models\Scol\Etablissement as ScolEtablissement;
use App\Models\Ref\NiveauFormationCohorte;
use App\Models\Ref\NiveauFormationParcours;


class NiveauFormationController extends Controller
{
    function syncNiveauFormation()
    {

        $scol_niv_forms = DB::connection('oracleScol')->table('NIVEAUX_SECTIONS ns')
            ->join('NIVEAUX_SECTION_FORMATIONS nsf', 'ns.CODE_NIV_SEC', '=', 'nsf.CODE_NIV_SEC')
            ->join('Formation fr', 'nsf.CODE_FORMATION', '=', 'fr.CODE_FORMATION')
            ->join('FORMATION_ACC_FORMATION faf', 'faf.ID_FORMATION', '=', 'fr.CODE_FORMATION')
            ->join('FORMATION_ACCREDITE fa', 'faf.ID_FORMATION_ACC', '=', 'fa.CODE_FORMATION')
            ->select(
                'ns.CODE_NIV_SEC',
                'ns.LIBELLE_LONG',
                'ns.LIBELLE_COURT',
                'ns.A_SELECTION',
                'ns.AUTORISATION_PERMISE',
                'ns.PRESENTIELLE',
                'ns.NBRE_INS_PERMISES',
                'ns.DIPLOMANTE',
                'fa.CODE_FORMATION',
                'ns.OUVERT',
                'fr.CODE_ED as CODE_EDOC',
                'ns.UTILISE_EVALUATION',
                'ns.TAG_SEMESTRIALISATION',
                'ns.TAG_GROUPEMATIERE',
                'ns.NIVEAU_CODE'
            )
            ->get();
        //dd($scol_niv_forms);
        foreach ($scol_niv_forms as $scol_niv_form) :
            //dump($scol_niv_form);
            $scolFormation = DB::connection('oracleScol')->table('FORMATION_ACCREDITE as fa')
                ->join('FORMATION_ACC_FORMATION as faf', 'fa.CODE_FORMATION', '=', 'faf.ID_FORMATION_ACC')
                ->join('Formation as fr', 'faf.ID_FORMATION', '=', 'fr.CODE_FORMATION')
                ->where('fa.code_formation', $scol_niv_form->code_formation)
                ->select('fa.*', 'fr.CODE_ED as CODE_EDOC')
                ->first();
            if ($scolFormation && (!$scolFormation->code_edoc)) :
                //dd($scolFormation);
                $lib_crt = '';
                if (str_contains($scolFormation->libelle_long, '(')) {
                    $pos = strpos($scolFormation->libelle_long, '(');
                    $lib_crt = substr($scolFormation->libelle_long, $pos);
                    //$lib = explode(" ",Str::ascii(substr($scolFormation->libelle_long,0,$pos)));
                } else {
                    $lib = explode(" ", Str::ascii($scolFormation->libelle_long));
                    for ($i = 0; $i < sizeof($lib); $i++) {
                        if (str_contains($lib[$i], "'")) {
                            $lib_crt .= strtoupper(substr($lib[$i], 2, 1));
                        } else if (strlen($lib[$i]) > 3) {
                            $lib_crt .= strtoupper(substr($lib[$i], 0, 1));
                        }
                    }
                }
                //dump("libelle court $lib_crt");
                $scolcycle = ScolCycle::where('code', $scolFormation->cycle_code)->first();
                if ($scolcycle) {
                    $cycle = Cycle::where('libelle_long', $scolcycle->libelle_long)
                        ->where('libelle_court', $scolcycle->libelle_court)->first();
                }
                if (empty($scolFormation->type_formation)) :
                    $typeFormation = 1;
                else :
                    $scolTypeFormation = ScolTypeFormation::where('type_formation', $scolFormation->type_formation)->first();
                    if ($scolTypeFormation) {
                        $typeFormation = TypeFormation::where('libelle_long', $scolTypeFormation->libelle_type_formation)->first();
                    }
                endif;
                $scolMention = ScolMention::where('code_mention', $scolFormation->code_mention)->first();
                if ($scolMention) {
                    $mention = Mention::where('libelle_long', $scolMention->lib_long_mention)
                        ->where('libelle_court', $scolMention->lib_court_mention)->first();
                }
                $scolGradeFormation = ScolGradeFormation::where('code_grade_diplome', $scolFormation->code_grade_diplome)->first();
                if ($scolGradeFormation) {
                    $gradeFormation = GradeFormation::where('libelle_long', $scolGradeFormation->libelle_long)
                        ->where('libelle_court', $scolGradeFormation->libelle_grade)->first();
                }
                $scolDepartement = ScolDepartement::where('code', $scolFormation->code_departement)
                    ->where('type_section', '!=', null)
                    ->first();

                if ($scolDepartement->etablissem_code) {
                    $scolStruct = ScolEtablissement::where('code', $scolDepartement->etablissem_code)->first();
                    if ($scolStruct) {
                        $struct = Etablissement::where('libelle_long', strtoupper(Str::ascii($scolStruct->libelle)))
                            ->where('libelle_court', strtoupper(str_replace(".", "", $scolStruct->sigle)))
                            ->first();
                        if ($struct) {
                            $departement = Departement::where('libelle_long', strtoupper(Str::ascii(str_replace(".", "", $scolDepartement->libelle_long))))
                                ->where('libelle_court', strtoupper(Str::ascii(str_replace(".", "", $scolDepartement->libelle_court))))
                                ->where('id_structures', $struct->id_structure)
                                ->first();
                            if ($departement && $gradeFormation && $typeFormation && $cycle && $mention) {
                                $formation = Formation::where('libelle_long', $scolFormation->libelle_long)
                                    ->where('libelle_court', $lib_crt)
                                    ->where('id_mention', $mention->id_mention)
                                    ->where('id_cycle', $cycle->id_cycle)
                                    ->where('id_type_formation', $typeFormation = 1 ? 1 : $typeFormation->id_type_formation)
                                    ->where('id_grades_formation', $gradeFormation->id_grades_formation)
                                    ->where('id_historiques_structure', $departement->id_historiques_structure)
                                    ->first();
                                if ($formation) {
                                    $niveauformation = NiveauxFormation::where('LIBELLE_LONG', $scol_niv_form->libelle_long)
                                        ->where('LIBELLE_COURT', $scol_niv_form->libelle_court)
                                        ->where('ID_FORMATION', $formation->id_formation)
                                        ->where('ID_NIVEAU', $scol_niv_form->niveau_code)
                                        ->first();
                                    if ($niveauformation) {
                                        $niv_cohorte = NiveauFormationCohorte::where('ID_NIVEAU_FORMATION', $niveauformation->id_niveau_formation)
                                            ->where('ID_COHORTE', 1)
                                            ->first();
                                        if (!$niv_cohorte) {
                                            try {
                                                $nivformc = DB::connection('oracle')->table('niveau_formation_cohortes')->insert([
                                                    'ID_NIVEAU_FORMATION' => $niveauformation->id_niveau_formation,
                                                    'ID_COHORTE' => 1,
                                                    'UTI_CREE' => 33
                                                ]);

                                                if ($nivformc) {
                                                    $niv_cohorte = NiveauFormationCohorte::where('ID_NIVEAU_FORMATION', $niveauformation->id_niveau_formation)
                                                        ->where('ID_COHORTE', 1)
                                                        ->first();
                                                    $niv_prcrs = NiveauFormationParcours::where('ID_NIVEAU_FORMATION_COHORTE', $niv_cohorte->id_niveau_formation_cohorte)->first();
                                                    if (!$niv_prcrs) {
                                                        try {
                                                            $nivformprcrs = DB::connection('oracle')->table('Niveaux_formation_parcours')->insert([
                                                                'ID_NIVEAU_FORMATION_COHORTE' => $niv_cohorte->id_niveau_formation_cohorte,
                                                                //'ID_COHORTE' => 1,
                                                                'UTI_CREE' => 33
                                                            ]);
                                                        } catch (Exception $e) {
                                                            dump($e->getMessage());
                                                        }
                                                    }
                                                }
                                            } catch (Exception $e) {
                                                dump($e->getMessage());
                                            }
                                        } else {

                                            $niv_prcrs = NiveauFormationParcours::where('ID_NIVEAU_FORMATION_COHORTE', $niv_cohorte->id_niveau_formation_cohorte)->first();
                                            if (!$niv_prcrs) {
                                                try {
                                                    $nivformprcrs = DB::connection('oracle')->table('Niveaux_formation_parcours')->insert([
                                                        'ID_NIVEAU_FORMATION_COHORTE' => $niv_cohorte->id_niveau_formation_cohorte,
                                                        //'ID_COHORTE' => 1,
                                                        'UTI_CREE' => 33
                                                    ]);
                                                } catch (Exception $e) {
                                                    dump($e->getMessage());
                                                }
                                            }
                                        }
                                    } else {
                                        try {
                                            $nivform = DB::connection('oracle')->table('niveaux_formations')->insert([
                                                'LIBELLE_LONG' => $scol_niv_form->libelle_long,
                                                'LIBELLE_COURT' => $scol_niv_form->libelle_court,
                                                'ID_FORMATION' => $formation->id_formation,
                                                'ID_NIVEAU' => $scol_niv_form->niveau_code,
                                                'A_SELECTION' => $scol_niv_form->a_selection ? 1 : 0,
                                                'AUTORISATION_PERMISE' => $scol_niv_form->autorisation_permise,
                                                'UTILISE_EVALUATION' => $scol_niv_form->utilise_evaluation,
                                                'PRESENTIELLE' => ($scol_niv_form->presentielle ? 1 : 0),
                                                'NBRE_INS_PERMISE' => $scol_niv_form->nbre_ins_permises,
                                                'DIPLOMANTE' => $scol_niv_form->diplomante,
                                                'OUVERT' => $scol_niv_form->ouvert,
                                                'TAG_SEMESTRIALISATION' => $scol_niv_form->tag_semestrialisation,
                                                'TAG_GROUPE_MATIERE' => $scol_niv_form->tag_groupematiere,
                                                //'MODE_DISPATCHING' => $scol_niv_form-> ,
                                                'UTI_CREE' => 33
                                            ]);
                                            if ($nivform) {
                                                $niv = NiveauxFormation::where('LIBELLE_LONG', $scol_niv_form->libelle_long)
                                                    ->where('LIBELLE_COURT', $scol_niv_form->libelle_court)
                                                    ->where('ID_FORMATION', $formation->id_formation)
                                                    ->where('ID_NIVEAU', $scol_niv_form->niveau_code)
                                                    ->first();
                                                $niv_cohorte = NiveauFormationCohorte::where('ID_NIVEAU_FORMATION', $niv->id_niveau_formation)
                                                    ->where('ID_COHORTE', 1)
                                                    ->first();
                                                if (!$niv_cohorte) {
                                                    try {
                                                        $nivformc = DB::connection('oracle')->table('niveau_formation_cohortes')->insert([
                                                            'ID_NIVEAU_FORMATION' => $niv->id_niveau_formation,
                                                            'ID_COHORTE' => 1,
                                                            'UTI_CREE' => 33
                                                        ]);
                                                        if ($nivformc) {
                                                            $niv_cohorte = NiveauFormationCohorte::where('ID_NIVEAU_FORMATION', $niveauformation->id_niveau_formation)
                                                                ->where('ID_COHORTE', 1)
                                                                ->first();
                                                            $niv_prcrs = NiveauFormationParcours::where('ID_NIVEAU_FORMATION_COHORTE', $niv_cohorte->id_niveau_formation_cohorte)->first();
                                                            if (!$niv_prcrs) {
                                                                try {
                                                                    $nivformprcrs = DB::connection('oracle')->table('Niveaux_formation_parcours')->insert([
                                                                        'ID_NIVEAU_FORMATION_COHORTE' => $niv_cohorte->id_niveau_formation_cohorte,
                                                                        //'ID_COHORTE' => 1,
                                                                        'UTI_CREE' => 33
                                                                    ]);
                                                                } catch (Exception $e) {
                                                                    dump($e->getMessage());
                                                                }
                                                            }
                                                        }
                                                    } catch (Exception $e) {
                                                        dump($e->getMessage());
                                                    }
                                                } else {
                                                    $niv_prcrs = NiveauFormationParcours::where('ID_NIVEAU_FORMATION_COHORTE', $niv_cohorte->id_niveau_formation_cohorte)->first();
                                                    if (!$niv_prcrs) {
                                                        try {
                                                            $nivformprcrs = DB::connection('oracle')->table('Niveaux_formation_parcours')->insert([
                                                                'ID_NIVEAU_FORMATION_COHORTE' => $niv_cohorte->id_niveau_formation_cohorte,
                                                                //'ID_COHORTE' => 1,
                                                                'UTI_CREE' => 33
                                                            ]);
                                                        } catch (Exception $e) {
                                                            dump($e->getMessage());
                                                        }
                                                    }
                                                }
                                            }
                                        } catch (Exception $e) {
                                            dump($e->getMessage());
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            endif;
        endforeach;
        session_commit();
        return back()->with('success', 'Synchronisation Niveaux Formations effectué');
    }
}
/**
 * LibelleLong, UtiCree, LibelleCourt, IdNiveau, IdFormation, ASelection, AutorisationPermise, Presentielle, NbreInsPermise, Diplomante, Ouvert
 */
