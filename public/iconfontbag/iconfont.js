;(function(window) {

var svgSprite = '<svg>' +
  ''+
    '<symbol id="icon-mima" viewBox="0 0 1024 1024">'+
      ''+
      '<path d="M521.939373 555.506886c-41.249473 0-74.679863 33.429367-74.679863 74.679863 0 29.720905 17.511839 55.17974 42.674938 67.188238l0 82.138742c0 17.659195 14.34573 32.005948 32.004925 32.005948 17.659195 0 32.005948-14.346753 32.005948-32.005948l0-82.140789c25.162076-12.009522 42.674938-37.46631 42.674938-67.187215C596.620259 588.936253 563.188846 555.506886 521.939373 555.506886zM831.296093 406.177859l-640.016629 0c-35.352159 0-64.011896 28.626991-64.011896 63.980173l0 426.676729c0 35.352159 28.659737 64.011896 64.011896 64.011896l640.016629 0c35.31839 0 63.978127-28.659737 63.978127-64.011896L895.27422 470.158033C895.275243 434.805874 866.615507 406.177859 831.296093 406.177859zM831.296093 854.160847c0 35.352159-7.321756 42.674938-42.674938 42.674938L233.919611 896.835785c-35.351136 0-42.640146-7.321756-42.640146-42.674938L191.279465 512.832971c0-35.352159 7.28901-42.674938 42.640146-42.674938l554.701544 0c35.352159 0 42.674938 7.321756 42.674938 42.674938L831.296093 854.160847zM208.867028 373.975437c17.768689 0 32.218796-14.161534 32.705889-31.812543l0.067538 0.004093c28.92375-122.258485 138.525983-213.338876 269.63095-213.338876 102.480023 0 191.682647 55.771211 239.628636 138.475841 4.988618 11.293207 16.279778 19.177781 29.41903 19.177781 17.758456 0 32.154327-14.395872 32.154327-32.154327 0-6.928806-2.199085-13.33982-5.925966-18.58938-58.985416-102.048187-168.918177-170.922834-295.276027-170.922834-165.111478 0-302.614156 113.305579-334.272179 268.980127-0.149403 0.641613-0.280386 1.289366-0.391926 1.945305-0.153496 0.778736-0.321318 1.551332-0.469698 2.332115l0.154519 0.00921c-0.100284 1.040702-0.154519 2.094708-0.154519 3.162016C176.136579 359.321692 190.790324 373.975437 208.867028 373.975437z"  ></path>'+
      ''+
    '</symbol>'+
  ''+
    '<symbol id="icon-shouji" viewBox="0 0 1024 1024">'+
      ''+
      '<path d="M719.124849 957.385836 303.01171 957.385836c-61.285808 0-110.966301-50.096978-110.966301-111.923092L192.045409 173.913958c0-61.826114 49.680493-111.925139 110.966301-111.925139l416.113139 0c61.286832 0 110.967324 50.099025 110.967324 111.925139l0 671.548786C830.092174 907.288858 780.411681 957.385836 719.124849 957.385836zM774.600325 173.913958c0-30.895661-24.839735-55.962569-55.475476-55.962569L303.01171 117.951389c-30.633694 0-55.482639 25.066909-55.482639 55.962569l0 27.981285 527.071254 0L774.600325 173.913958zM774.600325 257.857812 247.529071 257.857812l0 446.828698 527.071254 0L774.600325 257.857812zM774.600325 760.647033 247.529071 760.647033l0 84.815711c0 30.912034 24.848944 55.961546 55.482639 55.961546l416.113139 0c30.635741 0 55.475476-25.049512 55.475476-55.961546L774.600325 760.647033zM511.07135 873.444029c-22.973224 0-41.60149-18.790972-41.60149-41.971927 0-23.183002 18.628266-41.974997 41.60149-41.974997 22.971178 0 41.597397 18.790972 41.597397 41.974997C552.668747 854.653057 534.042527 873.444029 511.07135 873.444029z"  ></path>'+
      ''+
    '</symbol>'+
  ''+
    '<symbol id="icon-jingdongzhuanhuan" viewBox="0 0 2790 1024">'+
      ''+
      '<path d="M1080.131628 810.457585c-67.083571 61.198208-168.474504 92.251873-301.254041 92.251873l-88.471843 0L690.405744 156.895172l88.471843 0c132.779537 0 234.17047 30.910119 301.254041 91.964782 66.078753 60.097693 101.008145 157.230111 101.008145 280.918437C1181.139773 653.179625 1146.210382 750.216346 1080.131628 810.457585zM1173.340471 159.622535c-94.261509-82.395086-233.357046-124.16681-413.410883-124.16681l-202.686169 0L557.243419 1023.479026l202.686169 0c180.053837 0 319.197222-41.723876 413.410883-124.071113 93.065297-81.294571 142.157839-209.145714 142.157839-369.629522C1315.49831 368.911795 1266.405768 240.869257 1173.340471 159.622535z"  ></path>'+
      ''+
      '<path d="M282.306043 632.22199c0 68.136238-4.737 118.568538-14.019605 146.081415-9.091212 27.321483-23.01512 50.09736-45.216815 67.514208-25.694635 19.570029-56.891845 35.886361-92.682509 44.977573-34.307361 8.612727-76.940359 13.110484-126.846325 13.54112L0 904.336306l0 119.621205 3.540788 0c3.492939 0.095697 6.124606 0 7.320818 0 137.803628 0 243.405227-29.809604 309.292586-86.079419 66.987875-57.274633 100.912448-144.885203 100.912448-260.247893L421.06664 33.493937 282.306043 33.493937 282.306043 632.22199z"  ></path>'+
      ''+
      '<path d="M1392.677911 853.138431c-29.61821 0-53.733845 23.972089-53.733845 53.638148 0 29.522513 24.115635 53.5903 53.733845 53.5903s53.733845-24.067786 53.733845-53.5903C1446.411756 877.11052 1422.296122 853.138431 1392.677911 853.138431z"  ></path>'+
      ''+
      '<path d="M2016.909205 211.77738l-389.725884 0L1627.183321 156.512384l389.725884 0L2016.909205 211.77738zM2095.8592 256.946347 2095.8592 110.960629 1548.233326 110.960629 1548.233326 256.946347l0 0.382788 232.687167 0 0 139.478324c0 25.790332-4.545606 38.613725-13.397575 46.365179-8.947666 7.799303-22.632332 11.914272-40.719058 12.249211l0 43.49427c46.987209-1.483303 81.390268-11.627181 103.304872-30.623028 21.723211-18.90015 32.728362-47.992027 32.728362-86.892843L1862.837093 257.329135l233.022106 0L2095.8592 256.946347z"  ></path>'+
      ''+
      '<path d="M1863.172033 0.287091 1780.920493 0.287091 1780.920493 34.642301 1528.328357 34.642301 1528.328357 80.194055 2115.812017 80.194055 2115.812017 34.642301 1863.172033 34.642301Z"  ></path>'+
      ''+
      '<path d="M1528.328357 497.528514l46.556573 0c99.572691-86.605752 125.602265-210.006987 125.602265-210.006987l-89.955146 0C1599.622595 374.845006 1562.779264 448.435971 1528.328357 497.528514z"  ></path>'+
      ''+
      '<path d="M1943.701028 287.521527c0 0 25.886029 123.401235 125.458719 210.006987l46.65227 0c-34.498755-49.092542-71.294238-122.635659-82.25154-210.006987L1943.701028 287.521527z"  ></path>'+
      ''+
      '<path d="M2202.130678 497.528514l46.556573 0c99.572691-86.605752 125.506568-210.006987 125.506568-210.006987l-89.8116 0C2273.377067 374.845006 2236.581585 448.435971 2202.130678 497.528514z"  ></path>'+
      ''+
      '<path d="M2617.4555 287.521527c0 0 25.933877 123.401235 125.506568 210.006987L2789.566489 497.528514c-34.450907-49.092542-71.294238-122.635659-82.203692-210.006987L2617.4555 287.521527z"  ></path>'+
      ''+
      '<path d="M2408.644725 34.642301 2424.769664 0l-82.395086 0-16.172787 34.642301L2201.987132 34.642301l0 45.551755 102.922084 0-82.777874 177.13508 238.333288 0 0 139.478324c0 26.938695-4.449909 38.613725-13.349726 46.365179-8.995515 7.799303-22.584483 11.914272-40.67121 12.249211l0 43.49427c46.939361-1.483303 81.342419-11.627181 103.209175-30.623028 21.771059-18.90015 32.824059-47.992027 32.824059-86.892843L2542.476929 257.329135l226.993198 0 0-2.20103 0-43.350725-226.993198 0L2542.476929 113.927235l-82.012298 0 0 97.850145-133.64081 0 60.863269-131.583325L2789.566489 80.194055 2789.566489 34.642301 2408.644725 34.642301z"  ></path>'+
      ''+
      '<path d="M2112.127684 952.998212c-79.476328 0-144.167476-64.547602-144.167476-143.928233 0-79.332783 64.691147-143.880385 144.167476-143.880385 79.524177 0 144.215324 64.547602 144.215324 143.880385C2256.343008 888.45061 2191.65186 952.998212 2112.127684 952.998212zM2112.127684 594.230295c-118.664235 0-215.222471 96.366842-215.222471 214.839683 0 118.520689 96.558236 214.887532 215.222471 214.887532 118.712083 0 215.222471-96.366842 215.222471-214.887532C2327.350155 690.597138 2230.839767 594.230295 2112.127684 594.230295z"  ></path>'+
      ''+
      '<path d="M2718.798584 612.36487 2582.621805 867.253733 2446.397178 612.36487 2376.394849 612.36487 2376.394849 1005.918633 2444.674632 1005.918633 2444.674632 755.958164 2582.621805 1004.674572 2720.52113 755.910315 2720.52113 1005.918633 2788.89661 1005.918633 2788.89661 612.36487Z"  ></path>'+
      ''+
      '<path d="M1838.769307 916.920457c-26.412362 23.302211-60.19339 36.077755-95.457721 36.077755-79.42848 0-144.071779-64.499753-144.071779-143.832536 0-79.380631 64.643299-143.832536 144.071779-143.832536 35.264331 0 69.093208 12.823393 95.50557 36.173452l7.655757 6.507394 50.09736-50.049512-8.373484-7.655757c-39.666391-36.077755-91.151358-55.982724-144.885203-55.982724-118.616386 0-215.126774 96.366842-215.126774 214.839683 0 118.424992 96.510388 214.791835 215.126774 214.791835 53.685997 0 105.170963-19.904968 145.028748-56.078421l8.18209-7.607909-50.09736-50.001663L1838.769307 916.920457z"  ></path>'+
      ''+
    '</symbol>'+
  ''+
    '<symbol id="icon-right" viewBox="0 0 1024 1024">'+
      ''+
      '<path d="M716.347077 504.379077 403.140923 207.596308c-15.714462-14.907077-41.216-14.907077-56.950154 0-15.734154 14.887385-15.734154 39.049846 0 53.956923l284.711385 269.804308L346.210462 801.181538c-15.734154 14.907077-15.734154 39.049846 0 53.956923 15.714462 14.907077 41.216 14.907077 56.950154 0l313.206154-296.782769C732.081231 543.448615 732.081231 519.286154 716.347077 504.379077z"  ></path>'+
      ''+
    '</symbol>'+
  ''+
    '<symbol id="icon-jiantouxia" viewBox="0 0 1024 1024">'+
      ''+
      '<path d="M892.430964 380.448872 842.623581 315.904857 512.011768 597.918835 181.376419 315.904857 131.56699 380.448872 511.930927 708.095143 512.012791 707.996906 512.093633 708.095143Z"  ></path>'+
      ''+
    '</symbol>'+
  ''+
    '<symbol id="icon-tb" viewBox="0 0 2047 1024">'+
      ''+
      '<path d="M1035.093662 857.757768l0-56.8201c0-47.322795-22.446221-67.511603-82.372056-67.511603-24.234961 0-38.750559 2.001587-44.660152 6.613628-4.841262 3.751441-8.641821 11.254322-13.852496 21.661346 16.286941-4.321422 31.869848-6.668886 47.788399-6.668886 33.239032 0 47.7577 11.851932 47.7577 38.361703 0 2.89698-0.326435 6.098905-0.708128 9.267061-9.31311-12.130272-25.600051-19.076474-43.623543-19.076474-39.799448 0-66.087161 23.38357-66.087161 60.606333 0 36.625152 25.249057 60.891835 61.266365 60.891835 21.073968 0 38.653345-7.468089 50.473555-21.549805 0 0 2.184759 10.05296 13.025665 15.735379 0.218988 0.069585 0.458441 0.194428 0.671289 0.326435 0.360204 0.146333 0.712221 0.340761 1.104147 0.520863 0.896416 0.437975 1.865487 0.76441 2.799766 1.098008 0.146333 0.014326 0.301875 0.041956 0.427742 0.097214 0.645706 0.194428 1.281179 0.361227 1.945305 0.48607 2.973728 0.76441 6.297426 1.221828 10.218736 1.221828 30.65723 0 31.53932-24.758893 31.53932-24.758893C1036.496615 878.259708 1035.093662 872.493377 1035.093662 857.757768zM959.301466 879.69029c-17.621333 0-30.813796-13.880125-30.813796-36.069497 0-22.549575 12.452613-35.770691 31.522947-35.770691 17.992793 0 30.111808 12.400425 30.111808 35.187407C990.121402 866.685092 977.652416 879.69029 959.301466 879.69029z"  ></path>'+
      ''+
      '<path d="M561.903573 733.120097c-51.596122 0-83.802638 31.456433-83.802638 86.268806 0 54.520731 32.207539 86.268806 83.802638 86.268806 51.603285 0 84.126003-31.747051 84.126003-86.268806C646.029576 764.862032 613.506858 733.120097 561.903573 733.120097zM561.903573 880.815927c-22.164812 0-32.540114-20.438495-32.540114-61.426001 0-40.959877 10.375302-61.460794 32.540114-61.460794 21.809725 0 32.901341 20.500916 32.901341 61.460794C594.804914 860.377432 584.054059 880.815927 561.903573 880.815927z"  ></path>'+
      ''+
      '<path d="M776.742617 734.566028c-20.764929 0-36.027541 6.071276-46.733371 18.742876l0-37.208437 0-14.575973c0 0 0.034792-0.964978-0.14224-2.52859-0.027629-0.285502-0.040932-0.562818-0.075725-0.784876-0.052189-0.361227-0.104377-0.660032-0.166799-1.02126-0.545422-3.362584-1.690502-6.503111-3.338025-9.323343-0.197498-0.396019-0.451278-0.813528-0.712221-1.167592-0.11768-0.194428-0.211824-0.402159-0.37453-0.576121-0.388856-0.645706-0.848321-1.257643-1.344624-1.820462-0.062422-0.090051-0.181125-0.194428-0.271176-0.284479-0.385786-0.479931-0.812505-0.972141-1.282203-1.444908-0.159636-0.166799-0.301875-0.340761-0.48914-0.487094-0.337691-0.346901-0.680499-0.666172-1.073448-0.986467-2.514263-2.215458-5.431709-3.973498-8.621355-5.126764-4.720511-1.87572-10.618848-3.062755-18.111496-3.062755-19.531845 0-26.673499 11.941983-26.673499 11.941983l0 0c-2.942005 3.793396-4.877077 8.377808-5.492084 13.401218 16.673751 0.436952 20.035312 6.300496 20.035312 23.057135l0 136.447648c0 10.379395 0.569982 21.369703-17.635659 20.500916 0 0 0.094144 1.111311 0.483001 2.820232 0.020466 0.068562 0.045025 0.097214 0.045025 0.097214l0 0.152473c1.452071 5.953595 6.395664 18.882046 22.331611 20.869307 2.580778 0.535189 5.439896 0.819668 8.638751 0.819668 26.051329 0 31.154557-20.139689 31.154557-20.139689 11.441587 14.400988 29.448706 22.202675 49.484018 22.202675 43.655266 0 74.458829-32.589233 74.458829-87.748507C850.836125 765.146511 822.092477 734.566028 776.742617 734.566028zM764.275677 879.127472c-21.11183 0-36.027541-18.750039-36.027541-52.81181l0-17.054421c0-33.43653 14.238282-52.18657 34.985816-52.18657 23.212678 0 36.002982 21.632693 36.002982 63.996546C799.236933 859.502505 786.769994 879.127472 764.275677 879.127472z"  ></path>'+
      ''+
      '<path d="M1154.971937 733.120097c-51.603285 0-83.773985 31.456433-83.773985 86.268806 0 54.520731 32.171724 86.268806 83.773985 86.268806 51.596122 0 84.136236-31.747051 84.136236-86.268806C1239.109196 764.862032 1206.569082 733.120097 1154.971937 733.120097zM1154.971937 880.815927c-22.146393 0-32.525788-20.438495-32.525788-61.426001 0-40.959877 10.379395-61.460794 32.525788-61.460794 21.800515 0 32.860409 20.500916 32.860409 61.460794C1187.832346 860.377432 1177.133679 880.815927 1154.971937 880.815927z"  ></path>'+
      ''+
      '<path d="M1425.040862 755.338121c10.733459 0 19.138896 3.306302 25.822109 12.226462 3.361561 4.501524 8.586562 7.856945 14.567787 8.54563 12.018731 1.410116 21.516036-6.626931 21.516036-25.968441 0 0-27.52489-17.020652-62.98245-17.020652-52.630684 0-90.352821 33.762965-90.352821 87.429235 0 52.221362 35.666314 85.108377 87.928608 85.108377 26.315342 0 48.803519-9.259898 64.405869-25.967418-6.252401-5.779633-10.739599-8.670473-17.659195-8.670473-9.349949 0-21.813818 8.405437-38.09769 8.405437-26.988678 0-45.350884-23.411199-45.350884-64.642253C1384.837208 776.970814 1402.1362 755.338121 1425.040862 755.338121z"  ></path>'+
      ''+
      '<path d="M1594.223053 733.120097c-51.596122 0-83.802638 31.456433-83.802638 86.268806 0 54.520731 32.206516 86.268806 83.802638 86.268806 51.561329 0 84.108606-31.747051 84.108606-86.268806C1678.331659 764.862032 1645.784382 733.120097 1594.223053 733.120097zM1594.223053 880.815927c-22.188348 0-32.567743-20.438495-32.567743-61.426001 0-40.959877 10.379395-61.460794 32.567743-61.460794 21.800515 0 32.873712 20.500916 32.873712 61.460794C1627.096764 860.377432 1616.370469 880.815927 1594.223053 880.815927z"  ></path>'+
      ''+
      '<path d="M1287.744893 857.994152c-13.171997 0-23.815405 10.671037-23.815405 23.821545 0 13.1853 10.643408 23.842011 23.815405 23.842011 13.171997 0 23.828708-10.656711 23.828708-23.842011C1311.573601 868.665189 1300.91689 857.994152 1287.744893 857.994152z"  ></path>'+
      ''+
      '<path d="M311.853587 690.99365c-12.821003 3.765767-37.382399 4.640694-73.395614 4.640694-43.624567 0-72.735581-1.736551-87.609337-1.736551-24.548092 0-34.266431 6.335289-42.915415 31.150464 14.189164-4.932336 31.835056-5.182023 53.651944-5.182023 16.610306 0 19.399839 1.9934 19.399839 13.823843l0 68.567655 0 67.636446 0 0.958838c0 13.449313 6.113231 23.682375 16.981766 28.739555 2.067079 1.041726 4.237511 1.771343 6.519484 2.202155 3.292999 0.799202 6.877641 1.221828 10.771321 1.221828 33.689287 0 39.417755-24.828478 39.417755-24.828478-16.655332-0.416486-20.032242-6.265704-20.032242-23.036669L234.643088 734.252897c0-12.670577 4.835122-13.227256 22.157649-13.227256 5.885034 0 10.028401 0.285502 12.452613 0.285502C294.888194 721.311143 301.80779 716.384947 311.853587 690.99365z"  ></path>'+
      ''+
      '<path d="M1965.363931 857.19495l0-63.802118c0-41.210587-17.284665-60.272735-57.438177-60.272735-20.772093 0-38.445614 7.774057-50.532907 22.522969-9.739828-15.005762-25.988908-22.807449-48.504714-22.807449-20.772093 0-36.923958 6.300496-48.303122 19.056008 0 0-3.549849-16.526395-32.019251-16.526395-8.107655 0-14.046924 2.062985-18.256806 4.452405-0.076748 0.056282-0.152473 0.062422-0.229221 0.13917-5.773494 3.361561-8.170077 7.321756-8.170077 7.321756l0.021489 0.013303c-2.730181 3.696182-4.571109 8.100492-5.126764 12.900821 16.610306 0.479931 20.00052 6.300496 20.00052 23.029506l0 118.975718 46.405913 0L1763.210815 795.429211c0-25.946952 8.648984-38.35454 29.40675-38.35454 17.680684 0 25.273616 10.379395 25.273616 31.449269l0 113.674992 46.364981 0L1864.256162 795.429211c0-25.946952 8.322549-38.35454 29.490661-38.35454 17.631566 0 25.238824 10.379395 25.238824 31.449269l0 80.190367c0 0-0.11154 3.507894 0.757247 8.239662 0.041956 0.291642 0.090051 0.576121 0.166799 0.861624 0.090051 0.597611 0.242524 1.270946 0.451278 1.917676 0.13917 0.569982 0.278339 1.1328 0.430812 1.708921 0.11768 0.375553 0.249687 0.694825 0.339738 1.090844 0.173962 0.465604 0.354064 0.964978 0.521886 1.444908 0.138146 0.326435 0.264013 0.680499 0.403183 1.034563 0.180102 0.487094 0.423649 0.972141 0.62524 1.459235 0.138146 0.304945 0.313132 0.62524 0.451278 0.88209 0.833995 1.76418 1.868557 3.403516 3.035126 4.904707 0.208754 0.332575 0.445138 0.610914 0.715291 0.916882 0.298805 0.367367 0.62524 0.73678 0.966001 1.063215 0.222057 0.277316 0.451278 0.555655 0.700965 0.777713 0.104377 0.11154 0.188288 0.181125 0.291642 0.265036 0.319272 0.304945 0.611937 0.541329 0.889253 0.805342 0.409322 0.354064 0.820692 0.708128 1.264806 1.041726 0.354064 0.236384 0.687662 0.528026 1.069355 0.708128 0.430812 0.347924 0.924045 0.62524 1.340531 0.883113 0.333598 0.214894 0.680499 0.409322 1.056052 0.590448 4.389984 2.319835 10.045797 3.709485 17.061584 3.709485 30.692023 0 31.526017-24.758893 31.526017-24.758893C1966.634878 878.259708 1965.363931 871.617427 1965.363931 857.19495z"  ></path>'+
      ''+
      '<path d="M442.066231 857.757768l0-56.8201c0-47.322795-22.508643-67.511603-82.409918-67.511603-24.227798 0-38.767955 2.001587-44.648896 6.613628-4.880147 3.751441-8.645914 11.254322-13.852496 21.661346 16.280802-4.321422 31.848359-6.668886 47.78226-6.668886 33.238009 0 47.775096 11.851932 47.775096 38.361703 0 2.89698-0.340761 6.098905-0.708128 9.267061-9.347902-12.130272-25.610284-19.076474-43.6348-19.076474-39.799448 0-66.11479 23.38357-66.11479 60.606333 0 36.625152 25.286919 60.891835 61.297065 60.891835 21.060665 0 38.678928-7.468089 50.446949-21.549805 0 0 2.184759 10.05296 13.080923 15.735379 0.173962 0.069585 0.388856 0.194428 0.597611 0.326435 0.403183 0.146333 0.754177 0.340761 1.121544 0.520863 0.906649 0.437975 1.858324 0.76441 2.806929 1.098008 0.135076 0.014326 0.304945 0.041956 0.433882 0.097214 0.649799 0.194428 1.278109 0.361227 1.948375 0.48607 2.962471 0.76441 6.314823 1.221828 10.218736 1.221828 30.668487 0 31.491225-24.758893 31.491225-24.758893C443.428252 878.259708 442.066231 872.493377 442.066231 857.757768zM366.238218 879.69029c-17.686824 0-30.810726-13.880125-30.810726-36.069497 0-22.549575 12.484336-35.770691 31.495318-35.770691 18.006096 0 30.129204 12.400425 30.129204 35.187407C397.052015 866.685092 384.600425 879.69029 366.238218 879.69029z"  ></path>'+
      ''+
      '<path d="M125.309984 245.699911l-34.923394 53.898561 64.705698 40.258912c0 0 43.054585 22.084994 22.608927 63.35391C158.288073 442.357873 64.169485 528.014742 64.169485 528.014742l84.469833 52.658314c58.272171-126.606513 54.580083-109.691261 69.297272-155.207921 15.040555-46.482661 18.226107-81.926918-7.266497-107.485013C177.76466 284.98873 174.200484 281.932114 125.309984 245.699911z"  ></path>'+
      ''+
      '<path d="M342.399277 177.415712c13.349029-23.206538 19.798928-38.331004 19.798928-38.331004l-77.907371-21.85475c0 0-31.435966 102.510722-87.471191 150.34517 0 0 54.197366 31.271214 53.561893 30.354332 15.616676-15.533788 29.493731-31.337729 41.713031-46.659693 12.387122-5.4225 24.408923-10.524704 36.094056-15.297404-14.363126 25.946952-37.708833 64.781422-60.981886 89.293699l32.81436 28.691459c0 0 22.349007-21.435195 46.795792-47.38931l27.878954 0 0 47.827285L265.974677 354.395496l0 38.275745L374.696868 392.671241l0 91.723028c-1.36816-0.052189-2.747577-0.066515-4.106528-0.152473-12.008498-0.611937-30.685883-2.605338-37.986149-14.099113-8.888438-14.013155-2.390443-39.838334-1.920746-55.607482l-74.893734 0-2.747577 1.37123c0 0-27.343765 123.022894 79.283718 120.278387 99.871615 2.709715 157.087735-27.760251 184.534853-48.89664l10.927887 40.699957 61.585637-25.537629-41.59228-102.084003-50.064232 15.539928 9.391904 35.013445c-12.903891 9.524934-27.746948 16.70752-43.568285 21.980617l0-80.228229 105.999173 0 0-38.275745L463.543382 354.396519l0-47.827285 106.440218 0 0-38.267559L380.847961 268.301675c13.605879-16.537651 24.318872-31.724539 27.163663-41.453111l-32.895201-8.909927c141.334959-50.602492 220.143862-41.891086 219.385593 41.015136L594.502016 477.651704c0 0 8.464789 75.178213-77.661777 69.717851l-46.458101-10.003841-10.942213 44.168965c0 0 200.915939 57.397245 217.328747-97.064686 16.418948-154.457838-4.099365-252.874311-4.099365-252.874311S654.517901 89.028663 342.399277 177.415712z"  ></path>'+
      ''+
      '<path d="M1237.219149 310.842561l0-10.264784 24.043602 23.099091c0 0 77.564563-48.671513 75.564-101.299127-2.062985-52.613288-52.853765-63.86761-132.120087-67.62212-24.286126-1.147126-68.234057 1.569752-117.502158 5.898337l0-34.086329-98.312096 0 0 43.919279c-56.257282 6.130627-107.547435 12.452613-133.741004 15.752776l0-18.652825-87.387279 0 0 154.037259 87.387279 0L855.151407 310.842561l133.109624 0 0 49.119721L834.955436 359.962282l0 40.778752 153.304572 0 0 118.507044L767.763105 519.248078l0 50.984185 553.300638 0 0-50.984185-233.220692 0L1087.84305 400.741034l171.600263 0 0-40.778752-171.600263 0L1087.84305 310.842561 1237.219149 310.842561zM855.150384 234.994082c73.701582-16.978696 322.073346-56.059783 367.855042-33.283034 54.777581 27.263947-18.298761 67.653843-18.298761 67.653843l0.74292 0.694825L855.150384 270.059716 855.150384 234.994082z"  ></path>'+
      ''+
      '<path d="M1969.727309 245.851361c-2.084475-30.680766-18.632359-103.431697-157.337421-116.853381-115.224278-11.146874-257.612219 41.133839-302.525128 59.157331l0-36.256762-96.890724 0 0 416.70768 96.890724 0L1509.86476 420.769182l25.058722 24.443715c0 0 30.900777-25.14161 67.157539-58.71731 17.735943 26.267247 30.17116 47.206138 30.17116 47.206138l53.80544-53.656037c-6.349615-13.272281-18.874883-28.062126-33.936927-42.72815 9.774621-10.288321 19.063171-20.654412 27.378557-30.748305 17.048281 13.713326 34.888602 32.386618 51.547003 51.946093-55.180763 77.710896-119.510908 105.874329-119.510908 105.874329l31.054273 42.151006c46.405913-24.439622 89.353051-62.123896 125.728516-100.978832 21.271466 28.698622 35.763528 51.152007 35.763528 51.152007l68.345598-57.519018c-7.419993-13.056364-28.956495-31.959899-54.716182-51.561329 27.913746-35.70827 48.255027-66.868967 58.189284-82.826404 1.362021 59.915601 2.682086 170.352853-1.417279 222.310202-6.224772 78.626755-84.893482 28.767184-84.893482 28.767184l-4.140297 42.169425c0 0 35.19457 23.022343 93.160773 32.588209 99.023294 16.402575 90.575902-95.02933 92.097558-154.200988C1972.269201 375.915625 1973.82565 306.347177 1969.727309 245.851361zM1797.863033 201.741747c-8.468882 40.848337-21.348214 75.872015-36.368302 105.921401-22.925129-15.446807-43.836391-28.765137-56.548924-36.718273 5.2864-9.236362 8.864902-17.72571 10.101055-25.096585l-72.458265-36.433794c0 0-15.242146 40.414455-40.898479 85.170798-31.143301-23.744797-58.480926-41.060161-58.480926-41.060161l-26.919093 34.474163c16.311501 9.632381 35.805484 31.054273 54.117548 54.552453-17.84032 23.710005-38.285978 44.822858-60.543911 57.487296L1509.863736 244.073878c82.315774-38.72293 208.600969-73.854055 286.644439-78.5848 45.468564-2.75781 64.983014 14.182001 72.916706 30.042223 4.001127 7.964392 5.182023 21.567201 5.390777 28.360931 0.173962 6.175653 0.430812 14.789844 0.708128 25.176402L1797.863033 201.741747z"  ></path>'+
      ''+
      '<path d="M171.483606 225.559199c30.703279 0 55.598272-22.299889 55.598272-49.848315 0-27.52489-24.894993-49.848315-55.598272-49.848315-30.692023 0-55.587016 22.323425-55.587016 49.848315C115.897613 203.25931 140.791583 225.559199 171.483606 225.559199z"  ></path>'+
      ''+
      '<path d="M1193.967066 509.063111c30.699186 0 55.597249-22.310122 55.597249-49.845245 0-27.523867-24.898063-49.852408-55.597249-49.852408-30.693046 0-55.591109 22.328541-55.591109 49.852408C1138.375957 486.752989 1163.27402 509.063111 1193.967066 509.063111z"  ></path>'+
      ''+
    '</symbol>'+
  ''+
'</svg>'
var script = function() {
    var scripts = document.getElementsByTagName('script')
    return scripts[scripts.length - 1]
  }()
var shouldInjectCss = script.getAttribute("data-injectcss")

/**
 * document ready
 */
var ready = function(fn){
  if(document.addEventListener){
      document.addEventListener("DOMContentLoaded",function(){
          document.removeEventListener("DOMContentLoaded",arguments.callee,false)
          fn()
      },false)
  }else if(document.attachEvent){
     IEContentLoaded (window, fn)
  }

  function IEContentLoaded (w, fn) {
      var d = w.document, done = false,
      // only fire once
      init = function () {
          if (!done) {
              done = true
              fn()
          }
      }
      // polling for no errors
      ;(function () {
          try {
              // throws errors until after ondocumentready
              d.documentElement.doScroll('left')
          } catch (e) {
              setTimeout(arguments.callee, 50)
              return
          }
          // no errors, fire

          init()
      })()
      // trying to always fire before onload
      d.onreadystatechange = function() {
          if (d.readyState == 'complete') {
              d.onreadystatechange = null
              init()
          }
      }
  }
}

/**
 * Insert el before target
 *
 * @param {Element} el
 * @param {Element} target
 */

var before = function (el, target) {
  target.parentNode.insertBefore(el, target)
}

/**
 * Prepend el to target
 *
 * @param {Element} el
 * @param {Element} target
 */

var prepend = function (el, target) {
  if (target.firstChild) {
    before(el, target.firstChild)
  } else {
    target.appendChild(el)
  }
}

function appendSvg(){
  var div,svg

  div = document.createElement('div')
  div.innerHTML = svgSprite
  svg = div.getElementsByTagName('svg')[0]
  if (svg) {
    svg.setAttribute('aria-hidden', 'true')
    svg.style.position = 'absolute'
    svg.style.width = 0
    svg.style.height = 0
    svg.style.overflow = 'hidden'
    prepend(svg,document.body)
  }
}

if(shouldInjectCss && !window.__iconfont__svg__cssinject__){
  window.__iconfont__svg__cssinject__ = true
  try{
    document.write("<style>.svgfont {display: inline-block;width: 1em;height: 1em;fill: currentColor;vertical-align: -0.1em;font-size:16px;}</style>");
  }catch(e){
    console && console.log(e)
  }
}

ready(appendSvg)


})(window)
