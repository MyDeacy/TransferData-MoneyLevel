<?php
namespace RuinPray\TransferData\MoneyLevel;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase {


	public function onEnable(){

		if(!is_dir($this->getDataFolder() . "old/")) {
			mkdir($this->getDataFolder() . "old", 0744, true);
		}
		$path = $this->getDataFolder() . "old/level.yml";
		if(file_exists($path)){
			if(($plugin = $this->getServer()->getPluginManager()->getPlugin("MoneyLevel")) !== null){
				var_dump($plugin->version);
				if(isset($plugin->version) && preg_match("/RE-CREATE/", $plugin->version)){
					$content = file_get_contents($path);
					$data = preg_replace("#^([ ]*)([a-zA-Z_]{1}[ ]*)\\:$#m", "$1\"$2\":", $content);
					$data = yaml_parse($data);
					$this->TransferData($plugin, $data);
				}else{
					$this->getLogger()->emergency("古いバージョンのMoneyLevelが検出されました。 現在引越しは不可能です。最新版を導入してください。\n\n");
				}
			}else{
				$this->getLogger()->emergency("MoneyLevelを検出できませんでした。");
			}
		}else{
			$this->getLogger()->emergency("旧データベースが検出できませんでした。");
		}
	}

	private function TransferData($plugin, array $data): void{
		$this->getLogger()->info("\n\n§b旧MoneyLevelのデータベースをお引越し中です。\nしばらくお待ちください...\n\n\n");
		foreach($data as $name => $lv){
			if($plugin->getLv($name) === false){
				$plugin->registerUser($name);
				$plugin->setLv($name, (int)$lv);
				echo "-";
			}
		}
		$this->getLogger()->info("\n\n§a正常に完了しました！！\n\n速やかにサーバーを閉じ、このプラグイン または 旧データベースを削除してください。");
	}
}