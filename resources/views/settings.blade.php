@extends('layouts.app')
@section('content')
    <div class="container" style="text-align: center; padding-bottom: 50px;">
        @if (session('status'))
            <div class="alert alert-primary">{{session('status')}}</div> @endif
        <h1>Settings</h1>
        <div style="text-align: left">
            <pre>Anti-Phishing code:@{{code}}</pre>
            <a href="/password/reset" class="btn btn-outline-danger">
                Reset password
            </a>
            <a href="/2fa" class="btn btn-outline-primary">2FA settings</a>
            <h5>On-site notifications and live updates:</h5>
            <input type="radio" id="one" name="n_picked" v-on:change="toggleNotifications"
                   :checked="this.is_notifications">
            <label for="one">Enabled</label>
            <br>
            <input type="radio" name="n_picked" id="two" v-on:change="toggleNotifications"
                   :checked="!this.is_notifications">
            <label for="two">Disabled</label>
            <br>
            <h5>Timezone select:</h5>
            <p>Time with your timezone: @{{ currentTime() }}</p>
            <p>Time with selected timezone: @{{ timeZoneTime() }} </p>
            <label for="area">Select area:</label>
            <select v-model="selected_area" id="area">
                <option value="Africa">Africa</option>
                <option value="America">America</option>
                <option value="Arctic">Arctic</option>
                <option value="Asia">Asia</option>
                <option value="Atlantic">Atlantic</option>
                <option value="Australia">Australia</option>
                <option value="Europe">Europe</option>
                <option value="Indian">Indian</option>
                <option value="Pacific">Pacific</option>
                <option value="UTC">UTC</option>
            </select>
            <br>
            <div v-if="this.selected_area!='UTC' && this.selected_area!=0">
                <div v-if="this.selected_area==='Africa'">
                    <label for="city">Select city:</label>
                    <select v-model="selected_city" id="city">
                        <option value="Abidjan">Abidjan</option>
                        <option value="Accra">Accra</option>
                        <option value="Addis_Ababa">Addis_Ababa</option>
                        <option value="Algiers">Algiers</option>
                        <option value="Asmara">Asmara</option>
                        <option value="Bamako">Bamako</option>
                        <option value="Bangui">Bangui</option>
                        <option value="Banjul">Banjul</option>
                        <option value="Bissau">Bissau</option>
                        <option value="Blantyre">Blantyre</option>
                        <option value="Brazzaville">Brazzaville</option>
                        <option value="Bujumbura">Bujumbura</option>
                        <option value="Cairo">Cairo</option>
                        <option value="Casablanca">Casablanca</option>
                        <option value="Ceuta">Ceuta</option>
                        <option value="Conakry">Conakry</option>
                        <option value="Dakar">Dakar</option>
                        <option value="Dar_es_Salaam">Dar_es_Salaam</option>
                        <option value="Djibouti">Djibouti</option>
                        <option value="Douala">Douala</option>
                        <option value="El_Aaiun">El_Aaiun</option>
                        <option value="Freetown">Freetown</option>
                        <option value="Gaborone">Gaborone</option>
                        <option value="Harare">Harare</option>
                        <option value="Johannesburg">Johannesburg</option>
                        <option value="Juba">Juba</option>
                        <option value="Kampala">Kampala</option>
                        <option value="Khartoum">Khartoum</option>
                        <option value="Kigali">Kigali</option>
                        <option value="Kinshasa">Kinshasa</option>
                        <option value="Lagos">Lagos</option>
                        <option value="Libreville">Libreville</option>
                        <option value="Lome">Lome</option>
                        <option value="Luanda">Luanda</option>
                        <option value="Lubumbashi">Lubumbashi</option>
                        <option value="Lusaka">Lusaka</option>
                        <option value="Malabo">Malabo</option>
                        <option value="Maputo">Maputo</option>
                        <option value="Maseru">Maseru</option>
                        <option value="Mbabane">Mbabane</option>
                        <option value="Mogadishu">Mogadishu</option>
                        <option value="Monrovia">Monrovia</option>
                        <option value="Nairobi">Nairobi</option>
                        <option value="Ndjamena">Ndjamena</option>
                        <option value="Niamey">Niamey</option>
                        <option value="Nouakchott">Nouakchott</option>
                        <option value="Ouagadougou">Ouagadougou</option>
                        <option value="Porto-Novo">Porto-Novo</option>
                        <option value="Sao_Tome">Sao_Tome</option>
                        <option value="Tripoli">Tripoli</option>
                        <option value="Tunis">Tunis</option>
                        <option value="Windhoek">Windhoek</option>
                    </select>
                </div>
                <div v-if="this.selected_area==='America'">
                    <label for="city">Select city:</label>
                    <select v-model="selected_city" id="city">
                        <option value="Adak">Adak</option>
                        <option value="Anchorage">Anchorage</option>
                        <option value="Anguilla">Anguilla</option>
                        <option value="Antigua">Antigua</option>
                        <option value="Araguaina">Araguaina</option>
                        <option value="Argentina/Buenos_Aires">Argentina/Buenos_Aires</option>
                        <option value="Argentina/Catamarca">Argentina/Catamarca</option>
                        <option value="Argentina/Cordoba">Argentina/Cordoba</option>
                        <option value="Argentina/Jujuy">Argentina/Jujuy</option>
                        <option value="Argentina/La_Rioja">Argentina/La_Rioja</option>
                        <option value="Argentina/Mendoza">Argentina/Mendoza</option>
                        <option value="Argentina/Rio_Gallegos">Argentina/Rio_Gallegos</option>
                        <option value="Argentina/Salta">Argentina/Salta</option>
                        <option value="Argentina/San_Juan">Argentina/San_Juan</option>
                        <option value="Argentina/San_Luis">Argentina/San_Luis</option>
                        <option value="Argentina/Tucuman">Argentina/Tucuman</option>
                        <option value="Argentina/Ushuaia">Argentina/Ushuaia</option>
                        <option value="Aruba">Aruba</option>
                        <option value="Asuncion">Asuncion</option>
                        <option value="Atikokan">Atikokan</option>
                        <option value="Bahia">Bahia</option>
                        <option value="Bahia_Banderas">Bahia_Banderas</option>
                        <option value="Barbados">Barbados</option>
                        <option value="Belem">Belem</option>
                        <option value="Belize">Belize</option>
                        <option value="Blanc-Sablon">Blanc-Sablon</option>
                        <option value="Boa_Vista">Boa_Vista</option>
                        <option value="Bogota">Bogota</option>
                        <option value="Boise">Boise</option>
                        <option value="Cambridge_Bay">Cambridge_Bay</option>
                        <option value="Campo_Grande">Campo_Grande</option>
                        <option value="Cancun">Cancun</option>
                        <option value="Caracas">Caracas</option>
                        <option value="Cayenne">Cayenne</option>
                        <option value="Cayman">Cayman</option>
                        <option value="Chicago">Chicago</option>
                        <option value="Chihuahua">Chihuahua</option>
                        <option value="Costa_Rica">Costa_Rica</option>
                        <option value="Creston">Creston</option>
                        <option value="Cuiaba">Cuiaba</option>
                        <option value="Curacao">Curacao</option>
                        <option value="Danmarkshavn">Danmarkshavn</option>
                        <option value="Dawson">Dawson</option>
                        <option value="Dawson_Creek">Dawson_Creek</option>
                        <option value="Denver">Denver</option>
                        <option value="Detroit">Detroit</option>
                        <option value="Dominica">Dominica</option>
                        <option value="Edmonton">Edmonton</option>
                        <option value="Eirunepe">Eirunepe</option>
                        <option value="El_Salvador">El_Salvador</option>
                        <option value="Fort_Nelson">Fort_Nelson</option>
                        <option value="Fortaleza">Fortaleza</option>
                        <option value="Glace_Bay">Glace_Bay</option>
                        <option value="Godthab">Godthab</option>
                        <option value="Goose_Bay">Goose_Bay</option>
                        <option value="Grand_Turk">Grand_Turk</option>
                        <option value="Grenada">Grenada</option>
                        <option value="Guadeloupe">Guadeloupe</option>
                        <option value="Guatemala">Guatemala</option>
                        <option value="Guayaquil">Guayaquil</option>
                        <option value="Guyana">Guyana</option>
                        <option value="Halifax">Halifax</option>
                        <option value="Havana">Havana</option>
                        <option value="Hermosillo">Hermosillo</option>
                        <option value="Indiana/Indianapolis">Indiana/Indianapolis</option>
                        <option value="Indiana/Knox">Indiana/Knox</option>
                        <option value="Indiana/Marengo">Indiana/Marengo</option>
                        <option value="Indiana/Petersburg">Indiana/Petersburg</option>
                        <option value="Indiana/Tell_City">Indiana/Tell_City</option>
                        <option value="Indiana/Vevay">Indiana/Vevay</option>
                        <option value="Indiana/Vincennes">Indiana/Vincennes</option>
                        <option value="Indiana/Winamac">Indiana/Winamac</option>
                        <option value="Inuvik">Inuvik</option>
                        <option value="Iqaluit">Iqaluit</option>
                        <option value="Jamaica">Jamaica</option>
                        <option value="Juneau">Juneau</option>
                        <option value="Kentucky/Louisville">Kentucky/Louisville</option>
                        <option value="Kentucky/Monticello">Kentucky/Monticello</option>
                        <option value="Kralendijk">Kralendijk</option>
                        <option value="La_Paz">La_Paz</option>
                        <option value="Lima">Lima</option>
                        <option value="Los_Angeles">Los_Angeles</option>
                        <option value="Lower_Princes">Lower_Princes</option>
                        <option value="Maceio">Maceio</option>
                        <option value="Managua">Managua</option>
                        <option value="Manaus">Manaus</option>
                        <option value="Marigot">Marigot</option>
                        <option value="Martinique">Martinique</option>
                        <option value="Matamoros">Matamoros</option>
                        <option value="Mazatlan">Mazatlan</option>
                        <option value="Menominee">Menominee</option>
                        <option value="Merida">Merida</option>
                        <option value="Metlakatla">Metlakatla</option>
                        <option value="Mexico_City">Mexico_City</option>
                        <option value="Miquelon">Miquelon</option>
                        <option value="Moncton">Moncton</option>
                        <option value="Monterrey">Monterrey</option>
                        <option value="Montevideo">Montevideo</option>
                        <option value="Montserrat">Montserrat</option>
                        <option value="Nassau">Nassau</option>
                        <option value="New_York">New_York</option>
                        <option value="Nipigon">Nipigon</option>
                        <option value="Nome">Nome</option>
                        <option value="Noronha">Noronha</option>
                        <option value="North_Dakota/Beulah">North_Dakota/Beulah</option>
                        <option value="North_Dakota/Center">North_Dakota/Center</option>
                        <option value="North_Dakota/New_Salem">North_Dakota/New_Salem</option>
                        <option value="Ojinaga">Ojinaga</option>
                        <option value="Panama">Panama</option>
                        <option value="Pangnirtung">Pangnirtung</option>
                        <option value="Paramaribo">Paramaribo</option>
                        <option value="Phoenix">Phoenix</option>
                        <option value="Port-au-Prince">Port-au-Prince</option>
                        <option value="Port_of_Spain">Port_of_Spain</option>
                        <option value="Porto_Velho">Porto_Velho</option>
                        <option value="Puerto_Rico">Puerto_Rico</option>
                        <option value="Punta_Arenas">Punta_Arenas</option>
                        <option value="Rainy_River">Rainy_River</option>
                        <option value="Rankin_Inlet">Rankin_Inlet</option>
                        <option value="Recife">Recife</option>
                        <option value="Regina">Regina</option>
                        <option value="Resolute">Resolute</option>
                        <option value="Rio_Branco">Rio_Branco</option>
                        <option value="Santarem">Santarem</option>
                        <option value="Santiago">Santiago</option>
                        <option value="Santo_Domingo">Santo_Domingo</option>
                        <option value="Sao_Paulo">Sao_Paulo</option>
                        <option value="Scoresbysund">Scoresbysund</option>
                        <option value="Sitka">Sitka</option>
                        <option value="St_Barthelemy">St_Barthelemy</option>
                        <option value="St_Johns">St_Johns</option>
                        <option value="St_Kitts">St_Kitts</option>
                        <option value="St_Lucia">St_Lucia</option>
                        <option value="St_Thomas">St_Thomas</option>
                        <option value="St_Vincent">St_Vincent</option>
                        <option value="Swift_Current">Swift_Current</option>
                        <option value="Tegucigalpa">Tegucigalpa</option>
                        <option value="Thule">Thule</option>
                        <option value="Thunder_Bay">Thunder_Bay</option>
                        <option value="Tijuana">Tijuana</option>
                        <option value="Toronto">Toronto</option>
                        <option value="Tortola">Tortola</option>
                        <option value="Vancouver">Vancouver</option>
                        <option value="Whitehorse">Whitehorse</option>
                        <option value="Winnipeg">Winnipeg</option>
                        <option value="Yakutat">Yakutat</option>
                        <option value="Yellowknife">Yellowknife</option>
                    </select>
                </div>
                <div v-if="this.selected_area==='Antarctica'">
                    <label for="city">Select city:</label>
                    <select v-model="selected_city" id="city">
                        <option value="Casey">Casey</option>
                        <option value="Davis">Davis</option>
                        <option value="DumontDUrville">DumontDUrville</option>
                        <option value="Macquarie">Macquarie</option>
                        <option value="Mawson">Mawson</option>
                        <option value="McMurdo">McMurdo</option>
                        <option value="Palmer">Palmer</option>
                        <option value="Rothera">Rothera</option>
                        <option value="Syowa">Syowa</option>
                        <option value="Troll">Troll</option>
                        <option value="Vostok">Vostok</option>
                    </select>
                </div>
                <div v-if="this.selected_area==='Arctic'">
                    <label for="city">Select city:</label>
                    <select v-model="selected_city" id="city">
                        <option value="Longyearbyen">Longyearbyen</option>
                    </select>
                </div>
                <div v-if="this.selected_area==='Asia'">
                    <label for="city">Select city:</label>
                    <select v-model="selected_city" id="city">
                        <option value="Aden">Aden</option>
                        <option value="Almaty">Almaty</option>
                        <option value="Amman">Amman</option>
                        <option value="Anadyr">Anadyr</option>
                        <option value="Aqtau">Aqtau</option>
                        <option value="Aqtobe">Aqtobe</option>
                        <option value="Ashgabat">Ashgabat</option>
                        <option value="Atyrau">Atyrau</option>
                        <option value="Baghdad">Baghdad</option>
                        <option value="Bahrain">Bahrain</option>
                        <option value="Baku">Baku</option>
                        <option value="Bangkok">Bangkok</option>
                        <option value="Barnaul">Barnaul</option>
                        <option value="Beirut">Beirut</option>
                        <option value="Bishkek">Bishkek</option>
                        <option value="Brunei">Brunei</option>
                        <option value="Chita">Chita</option>
                        <option value="Choibalsan">Choibalsan</option>
                        <option value="Colombo">Colombo</option>
                        <option value="Damascus">Damascus</option>
                        <option value="Dhaka">Dhaka</option>
                        <option value="Dili">Dili</option>
                        <option value="Dubai">Dubai</option>
                        <option value="Dushanbe">Dushanbe</option>
                        <option value="Famagusta">Famagusta</option>
                        <option value="Gaza">Gaza</option>
                        <option value="Hebron">Hebron</option>
                        <option value="Ho_Chi_Minh">Ho_Chi_Minh</option>
                        <option value="Hong_Kong">Hong_Kong</option>
                        <option value="Hovd">Hovd</option>
                        <option value="Irkutsk">Irkutsk</option>
                        <option value="Jakarta">Jakarta</option>
                        <option value="Jayapura">Jayapura</option>
                        <option value="Jerusalem">Jerusalem</option>
                        <option value="Kabul">Kabul</option>
                        <option value="Kamchatka">Kamchatka</option>
                        <option value="Karachi">Karachi</option>
                        <option value="Kathmandu">Kathmandu</option>
                        <option value="Khandyga">Khandyga</option>
                        <option value="Kolkata">Kolkata</option>
                        <option value="Krasnoyarsk">Krasnoyarsk</option>
                        <option value="Kuala_Lumpur">Kuala_Lumpur</option>
                        <option value="Kuching">Kuching</option>
                        <option value="Kuwait">Kuwait</option>
                        <option value="Macau">Macau</option>
                        <option value="Magadan">Magadan</option>
                        <option value="Makassar">Makassar</option>
                        <option value="Manila">Manila</option>
                        <option value="Muscat">Muscat</option>
                        <option value="Nicosia">Nicosia</option>
                        <option value="Novokuznetsk">Novokuznetsk</option>
                        <option value="Novosibirsk">Novosibirsk</option>
                        <option value="Omsk">Omsk</option>
                        <option value="Oral">Oral</option>
                        <option value="Phnom_Penh">Phnom_Penh</option>
                        <option value="Pontianak">Pontianak</option>
                        <option value="Pyongyang">Pyongyang</option>
                        <option value="Qatar">Qatar</option>
                        <option value="Qyzylorda">Qyzylorda</option>
                        <option value="Riyadh">Riyadh</option>
                        <option value="Sakhalin">Sakhalin</option>
                        <option value="Samarkand">Samarkand</option>
                        <option value="Seoul">Seoul</option>
                        <option value="Shanghai">Shanghai</option>
                        <option value="Singapore">Singapore</option>
                        <option value="Srednekolymsk">Srednekolymsk</option>
                        <option value="Taipei">Taipei</option>
                        <option value="Tashkent">Tashkent</option>
                        <option value="Tbilisi">Tbilisi</option>
                        <option value="Tehran">Tehran</option>
                        <option value="Thimphu">Thimphu</option>
                        <option value="Tokyo">Tokyo</option>
                        <option value="Tomsk">Tomsk</option>
                        <option value="Ulaanbaatar">Ulaanbaatar</option>
                        <option value="Urumqi">Urumqi</option>
                        <option value="Ust-Nera">Ust-Nera</option>
                        <option value="Vientiane">Vientiane</option>
                        <option value="Vladivostok">Vladivostok</option>
                        <option value="Yakutsk">Yakutsk</option>
                        <option value="Yangon">Yangon</option>
                        <option value="Yekaterinburg">Yekaterinburg</option>
                        <option value="Yerevan">Yerevan</option>
                    </select>
                </div>
                <div v-if="this.selected_area==='Atlantic'">
                    <label for="city">Select city:</label>
                    <select v-model="selected_city" id="city">
                        <option value="Azores">Azores</option>
                        <option value="Bermuda">Bermuda</option>
                        <option value="Canary">Canary</option>
                        <option value="Cape_Verde">Cape_Verde</option>
                        <option value="Faroe">Faroe</option>
                        <option value="Madeira">Madeira</option>
                        <option value="Reykjavik">Reykjavik</option>
                        <option value="South_Georgia">South_Georgia</option>
                        <option value="St_Helena">St_Helena</option>
                        <option value="Stanley">Stanley</option>
                    </select>
                </div>
                <div v-if="this.selected_area==='Australia'">
                    <label for="city">Select city:</label>
                    <select v-model="selected_city" id="city">
                        <option value="Adelaide">Adelaide</option>
                        <option value="Brisbane">Brisbane</option>
                        <option value="Broken_Hill">Broken_Hill</option>
                        <option value="Currie">Currie</option>
                        <option value="Darwin">Darwin</option>
                        <option value="Eucla">Eucla</option>
                        <option value="Hobart">Hobart</option>
                        <option value="Lindeman">Lindeman</option>
                        <option value="Lord_Howe">Lord_Howe</option>
                        <option value="Melbourne">Melbourne</option>
                        <option value="Perth">Perth</option>
                        <option value="Sydney">Sydney</option>
                    </select>
                </div>
                <div v-if="this.selected_area==='Europe'">
                    <label for="city">Select city:</label>
                    <select v-model="selected_city" id="city">
                        <option value="Amsterdam">Amsterdam</option>
                        <option value="Andorra">Andorra</option>
                        <option value="Astrakhan">Astrakhan</option>
                        <option value="Athens">Athens</option>
                        <option value="Belgrade">Belgrade</option>
                        <option value="Berlin">Berlin</option>
                        <option value="Bratislava">Bratislava</option>
                        <option value="Brussels">Brussels</option>
                        <option value="Bucharest">Bucharest</option>
                        <option value="Budapest">Budapest</option>
                        <option value="Busingen">Busingen</option>
                        <option value="Chisinau">Chisinau</option>
                        <option value="Copenhagen">Copenhagen</option>
                        <option value="Dublin">Dublin</option>
                        <option value="Gibraltar">Gibraltar</option>
                        <option value="Guernsey">Guernsey</option>
                        <option value="Helsinki">Helsinki</option>
                        <option value="Isle_of_Man">Isle_of_Man</option>
                        <option value="Istanbul">Istanbul</option>
                        <option value="Jersey">Jersey</option>
                        <option value="Kaliningrad">Kaliningrad</option>
                        <option value="Kiev">Kiev</option>
                        <option value="Kirov">Kirov</option>
                        <option value="Lisbon">Lisbon</option>
                        <option value="Ljubljana">Ljubljana</option>
                        <option value="London">London</option>
                        <option value="Luxembourg">Luxembourg</option>
                        <option value="Madrid">Madrid</option>
                        <option value="Malta">Malta</option>
                        <option value="Mariehamn">Mariehamn</option>
                        <option value="Minsk">Minsk</option>
                        <option value="Monaco">Monaco</option>
                        <option value="Moscow">Moscow</option>
                        <option value="Oslo">Oslo</option>
                        <option value="Paris">Paris</option>
                        <option value="Podgorica">Podgorica</option>
                        <option value="Prague">Prague</option>
                        <option value="Riga">Riga</option>
                        <option value="Rome">Rome</option>
                        <option value="Samara">Samara</option>
                        <option value="San_Marino">San_Marino</option>
                        <option value="Sarajevo">Sarajevo</option>
                        <option value="Saratov">Saratov</option>
                        <option value="Simferopol">Simferopol</option>
                        <option value="Skopje">Skopje</option>
                        <option value="Sofia">Sofia</option>
                        <option value="Stockholm">Stockholm</option>
                        <option value="Tallinn">Tallinn</option>
                        <option value="Tirane">Tirane</option>
                        <option value="Ulyanovsk">Ulyanovsk</option>
                        <option value="Uzhgorod">Uzhgorod</option>
                        <option value="Vaduz">Vaduz</option>
                        <option value="Vatican">Vatican</option>
                        <option value="Vienna">Vienna</option>
                        <option value="Vilnius">Vilnius</option>
                        <option value="Volgograd">Volgograd</option>
                        <option value="Warsaw">Warsaw</option>
                        <option value="Zagreb">Zagreb</option>
                        <option value="Zaporozhye">Zaporozhye</option>
                        <option value="Zurich">Zurich</option>
                    </select>
                </div>
                <div v-if="this.selected_area==='Indian'">
                    <label for="city">Select city:</label>
                    <select v-model="selected_city" id="city">
                        <option value="Antananarivo">Antananarivo</option>
                        <option value="Chagos">Chagos</option>
                        <option value="Christmas">Christmas</option>
                        <option value="Cocos">Cocos</option>
                        <option value="Comoro">Comoro</option>
                        <option value="Kerguelen">Kerguelen</option>
                        <option value="Mahe">Mahe</option>
                        <option value="Maldives">Maldives</option>
                        <option value="Mauritius">Mauritius</option>
                        <option value="Mayotte">Mayotte</option>
                        <option value="Reunion">Reunion</option>
                    </select>
                </div>
                <div v-if="this.selected_area==='Pacific'">
                    <label for="city">Select city:</label>
                    <select v-model="selected_city" id="city">
                        <option value="Apia">Apia</option>
                        <option value="Auckland">Auckland</option>
                        <option value="Bougainville">Bougainville</option>
                        <option value="Chatham">Chatham</option>
                        <option value="Chuuk">Chuuk</option>
                        <option value="Easter">Easter</option>
                        <option value="Efate">Efate</option>
                        <option value="Enderbury">Enderbury</option>
                        <option value="Fakaofo">Fakaofo</option>
                        <option value="Fiji">Fiji</option>
                        <option value="Funafuti">Funafuti</option>
                        <option value="Galapagos">Galapagos</option>
                        <option value="Gambier">Gambier</option>
                        <option value="Guadalcanal">Guadalcanal</option>
                        <option value="Guam">Guam</option>
                        <option value="Honolulu">Honolulu</option>
                        <option value="Kiritimati">Kiritimati</option>
                        <option value="Kosrae">Kosrae</option>
                        <option value="Kwajalein">Kwajalein</option>
                        <option value="Majuro">Majuro</option>
                        <option value="Marquesas">Marquesas</option>
                        <option value="Midway">Midway</option>
                        <option value="Nauru">Nauru</option>
                        <option value="Niue">Niue</option>
                        <option value="Norfolk">Norfolk</option>
                        <option value="Noumea">Noumea</option>
                        <option value="Pago_Pago">Pago_Pago</option>
                        <option value="Palau">Palau</option>
                        <option value="Pitcairn">Pitcairn</option>
                        <option value="Pohnpei">Pohnpei</option>
                        <option value="Port_Moresby">Port_Moresby</option>
                        <option value="Rarotonga">Rarotonga</option>
                        <option value="Saipan">Saipan</option>
                        <option value="Tahiti">Tahiti</option>
                        <option value="Tarawa">Tarawa</option>
                        <option value="Tongatapu">Tongatapu</option>
                        <option value="Wake">Wake</option>
                        <option value="Wallis">Wallis</option>
                    </select>
                </div>
            </div>
            <br>
            <button class="btn btn-outline-primary mt-2" @click="saveSettings()">Save settings</button>

        </div>
    </div>
@endsection
@push('js')
    <script src="{{asset('js/settings.js')}}"></script>
@endpush