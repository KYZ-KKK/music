<?php
// 咪咕音乐

namespace site;

class migu
{
    
    public static function search($query, $page)
    {
        if (empty($query)) {
            return;
        }
        $radio_search_url = [
            'method'         => 'GET',
            'url'            => 'http://pd.musicapp.migu.cn/MIGUM2.0/v1.0/content/search_all.do',
            'referer'        => 'http://music.migu.cn/',
            'proxy'          => false,
            'body'           => [
                'text'    => $query,
                // 'type'       => '2',
                'pageNo'        => $page,
                "pageSize" => "1",
                "searchSwitch" => '{"song":1,"album":0,"singer":0,"tagSong":0,"mvSong":0,"songlist":0,"bestShow":1}',

                // 'rows'       => 10
            ],
            'user-agent'    => 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1'
        ];
        $radio_result = mc_curl($radio_search_url);
        
        if (empty($radio_result)) {
            return;
        }
        $radio_data = json_decode($radio_result, true);
        // if (empty($radio_data['musics'])) {
        //     return;
        // }
        foreach ($radio_data["songResultData"]["result"] as $value) {
            $radio_song_id = $value['id'];
            $radio_author  = $value["singers"][0]['name'];
            //$radio_url  = self::getSongUrl($radio_song_id);
            
            // echo mc_curl($value["lyricUrl"]),
            if ($value["copyrightId"]) {
                $lyric=self::getLyric($value["copyrightId"]);
            }
            $radio_songs[] = [
                'type'   => 'migu',
                'link'   => 'https://music.migu.cn/v3/music/song/'.$radio_song_id,
                'songid' => $radio_song_id,
                'title'  => $value["name"],
                'author' => $value["singers"][0]['name'],
                'lrc'    => $lyric,
                'url'    => str_replace("ftp://218.200.160.122:21/",'https://freetyst.nf.migu.cn/',$value["newRateFormats"][0]['url']),
                'pic'    => $value["imgItems"][0]['img']
            ];
        }
        return $radio_songs;
    }




    public static function getLyric($songid)
    {
        $radio_lrc_url = [
            'method'        => 'GET',
            'url'           => 'http://music.migu.cn/v3/api/music/audioPlayer/getLyric',
            'referer'       => 'http://music.migu.cn/v3/music/player/audio',
            'proxy'         => false,
            'body'          => [
				'copyrightId' => $songid,
    			'accept'      => "application/json, text/plain, */*"
			]
        ];
        $radio_result = mc_curl($radio_lrc_url);
    
        $arr = json_decode($radio_result, true);
        // echo $arr;
        return isset($arr['lyric'])?$arr['lyric']:false;
    }

}