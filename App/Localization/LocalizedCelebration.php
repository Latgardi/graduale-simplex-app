<?php

namespace App\Localization;

class LocalizedCelebration
{
    private const REG_EXP = [
        "octavam_navitatis" =>'#De die\s([IV]+)\sinfra octavam Nativitatis#',
        "december" => "#Die\s([XIV]+)\sdecembris#",
        "hebdomada" => "#Hebdomada\s([XIV]+)\s(.+)#",
        "feria" => "#(.+),\shebdomada\s([XIV]+)\s(.+)#",
        "feria_hebdomadae_sanctae" => "#(.+)\s(Hebdomadae Sanctae)#",
        "infra_octavam" => "#(.+)\s(infra\soctavam)\s(.+)#",
        "post_octavam" => "#(.+)\s(post\soctavam)\s(.+)#",
        "post" => "#(.+)\s(post)\s(.+)#",
        "dominica" => "#Dominica\s([XIV]+)\s(.+)#",
        "feria_alt" => "#(.+)\s([XIV]+)\s(.+)#",
    ];
    public function __construct(
        public string $title
    ) {}

    public function getTranslation(): ?string
    {
        $localizedTitle = LocalizedName::for($this->title);
        if (is_null($localizedTitle)) {
            //var_dump($this->title);
            foreach (self::REG_EXP as $key => $regEx) {
                preg_match($regEx, $this->title, $matches);
                if (!empty($matches)) {
                    $localizedTitle = "";
                    switch ($key) {
                        case "octavam_navitatis":
                            $localizedTitle .= $matches[1] . ' ';
                            $localizedTitle .= (LocalizedName::for("die infra octavam Nativitatis"))
                                ?? "die infra octavam Nativitatis";
                            break;
                        case "december":
                            $localizedTitle .= $matches[1] . ' ';
                            $localizedTitle .= (LocalizedName::for("die") . ' ' . LocalizedName::for("decembris"))
                                ?? "die decembris";
                            break;
                        case "feria":
                            $localizedTitle .= (LocalizedName::for($matches[1]) ?? $matches[1]);
                            $localizedTitle .= ', ' . $matches[2] . ' ';
                            $localizedTitle .= (LocalizedName::for("hebdomada") ?? "hebdomada") . ' ';
                            $localizedTitle .= LocalizedName::for($matches[3]) ?? $matches[3];
                            break;
                        case "hebdomada":
                            $localizedTitle .= (LocalizedName::for($matches[1]) ?? $matches[1]);
                            $localizedTitle .= ' ' . (LocalizedName::for("hebdomada") ?? "hebdomada") . ' ';
                            $localizedTitle .= (LocalizedName::for($matches[2]) ?? $matches[2]);
                            break;
                        case "feria_hebdomadae_sanctae":
                            $localizedTitle .= (LocalizedName::for($matches[1]) ?? $matches[1]);
                            $localizedTitle .= ' ' . (LocalizedName::for($matches[2]) ?? $matches[2]);
                            break;
                        case "infra_octavam":
                        case "post_octavam":
                        case "post":
                            $localizedTitle .= (LocalizedName::for($matches[1]) ?? $matches[1]);
                            $localizedTitle .= ' ' . (LocalizedName::for($matches[2]) ?? $matches[2]) . ' ';
                            $localizedTitle .= LocalizedName::for($matches[3]) ?? $matches[3];
                            break;
                        case "dominica":
                            $localizedTitle .= $matches[1] . ' ';
                            $localizedTitle .= (LocalizedName::for('dominica') ?? 'Dominica') . ' ';
                            $localizedTitle .= LocalizedName::for($matches[2]) ?? $matches[2];
                            break;
                        case "feria_alt":
                            $localizedTitle .= (LocalizedName::for($matches[1]) ?? $matches[1]);
                            $localizedTitle .= ' ' . $matches[2] . ' ';
                            $localizedTitle .= LocalizedName::for($matches[3]) ?? $matches[3];
                    }
                    return $this->replaceShortU($localizedTitle);
                }
            }
        }
        return $localizedTitle;
    }

    private function replaceShortU(string $string): string
    {
        return preg_replace('#([аоуеыёіэяю])\sу#u', '$1 ў', $string);
    }

}
