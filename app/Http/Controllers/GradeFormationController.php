<?php

namespace App\Http\Controllers;

use App\Models\Ref\GradeFormation;
use App\Models\Scol\GradeFormation as GradeDiplomeEdit;
use Illuminate\Support\Facades\DB;
use Exception;

class GradeFormationController extends Controller
{
    public function synchGradeFormation()
    {
        //GRADE_DIPLOME_EDIT(Scol)
        $grades = GradeDiplomeEdit::all();
        foreach ($grades as $grade) {
            //GradesFormation(Refonte)
            $gradeform = GradeFormation::where('libelle_long', $grade->libelle_long)
                ->orwhere('libelle_court', $grade->libelle_grade)
                ->first();
            dump($gradeform);
            if (!$gradeform) {
                try {
                    DB::connection('oracle')->table('grades_formation')->insert([
                        'libelle_long' => $grade->libelle_long,
                        'libelle_court' => $grade->libelle_grade,
                        'uti_cree' => 33
                    ]);
                } catch (Exception $e) {
                    dump($e->getMessage());
                }

            }
        }
        session_commit();
        return back()->with('success', 'Synchronisation Grades Formations effectué');
    }
}
