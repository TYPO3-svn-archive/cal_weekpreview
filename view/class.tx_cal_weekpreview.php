<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Thomas Kowtsch 
*  All rights reserved
*
*  This script is based in particular on the "calendar base" extension (cal) 
*  which is part of the Web-Empowered Church (WEC).
* 
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once (t3lib_extMgm :: extPath('cal').'view/class.tx_cal_base_view.php');

/**
 *
 * @author Thomas Kowtsch <typo3(at)thomas-kowtsch.de>
 */
class tx_cal_weekpreview extends tx_cal_base_view {

        function tx_cal_weekpreview(){
                $this->tx_cal_base_view();
        }

        /**
         *  Looks for month markers.
         *  @param              $master_array   array           The event to be drawn.
         *  @param              $getdate                integer         The date of the event
         *  @return         string          The HTML output.
         */
        function drawMonth(&$master_array, $getdate) {
                //Resetting viewarray, to make sure we always get the current events
                $this->viewarray = false;
                $this->_init($master_array);
                $page = '';
                if($this->conf['view.']['month.']['monthMakeMiniCal']){
                        $page = $this->conf['view.']['month.']['monthMiniTemplate'];
                }else{
                        $page = $this->cObj->fileResource($this->conf['view.']['month.']['monthTemplate']);
                        if ($page == '') {
                                return '<h3>calendar: no template file found:</h3>'.$this->conf['view.']['month.']['monthTemplate'].'<br />Pl
ease check your template record and add both cal items at "include static (from extension)"';
                        }
                }

                $rems = array();
                
                return $this->finish($page, $rems);
        }

	/**
	 * Draws the month view.
	 * Basically, it is a copy from the original cal base code
	 *  @param		$page	string		The page template
	 *  @param		$offset	integer		The month offset. Default = +0
	 *  @param		$type	integer		The date of the event
	 *	@return		string		The HTML output.
	 */
	function _draw_month($page, $offset = '+0', $type) {
		
		$isEnabled = $this->conf['view.']['weekpreview.']['enable'];
		
		if (intval($isEnabled)!=0) {
			$numOfWeeks = intval($this->conf['view.']['weekpreview.']['numOfWeeks']);
			
			$viewTarget = $this->conf['view.']['monthLinkTarget'];
			$monthTemplate = $this->cObj->getSubpart($page, '###MONTH_TEMPLATE###');
			if($monthTemplate!=''){
				$loop_wd = $this->cObj->getSubpart($monthTemplate, '###LOOPWEEKDAY###');
				$t_month = $this->cObj->getSubpart($monthTemplate, '###SWITCHMONTHDAY###');
				$startweek = $this->cObj->getSubpart($monthTemplate, '###LOOPMONTHWEEKS_DAYS###');
				$endweek = $this->cObj->getSubpart($monthTemplate, '###LOOPMONTHDAYS_WEEKS###');
				$weeknum = $this->cObj->getSubpart($monthTemplate, '###LOOPWEEK_NUMS###');
				$corner = $this->cObj->getSubpart($monthTemplate, '###CORNER###');
	
				/* 11.12.2008 Franz:
				* why is there a limitation that only MEDIUM calendar sheets can have absolute offsets and vice versa?
				* I'm commenting this out and make it more flexible.
				*/
				#if ($type != 'medium') {  // old one
				if(preg_match('![+|-][0-9]{1,2}!is',$offset)) { // new one
					$fake_getdate_time = new tx_cal_date();
					$fake_getdate_time->copy($this->controller->getDateTimeObject);
					$fake_getdate_time->setDay(15);
					if(intval($offset)<0){
						$fake_getdate_time->subtractSeconds(abs(intval($offset))*2592000);
					} else {
						$fake_getdate_time->addSeconds(intval($offset)*2592000);
					}
				} else {
					$fake_getdate_time = new tx_cal_date();
					$fake_getdate_time->copy($this->controller->getDateTimeObject);
					$fake_getdate_time->setDay(15);
					$fake_getdate_time->setMonth($offset);
				}
				
				$minical_month = $fake_getdate_time->getMonth();
				$minical_year = $fake_getdate_time->getYear();
				$today = new tx_cal_date();
		
				$month_title = $fake_getdate_time->format($this->conf['view.'][$viewTarget.'.']['dateFormatMonth']);
				$this->initLocalCObject();
				$this->local_cObj->setCurrentVal($month_title);
				$this->local_cObj->data['view'] = $viewTarget;
				$this->controller->getParametersForTyposcriptLink($this->local_cObj->data, array ('getdate' => $fake_getdate_time->format('%Y%m%d'), 'view' => $viewTarget,  $this->pointerName => NULL), $this->conf['cache'], $this->conf['clear_anyway'], $this->conf['view.'][$viewTarget.'.'][$viewTarget.'ViewPid']);
				$month_title = $this->local_cObj->cObjGetSingle($this->conf['view.'][$viewTarget.'.'][$viewTarget.'ViewLink'],$this->conf['view.'][$viewTarget.'.'][$viewTarget.'ViewLink.']);
				$month_date = $fake_getdate_time->format('%Y%m%d');
		
				$view_array = array ();
	
				if(!$this->viewarray){
					$this->eventArray = array();
					if (!empty($this->master_array)) {
						// use array keys for the loop in order to be able to use referenced events instead of copies and save some memory
						$masterArrayKeys = array_keys($this->master_array);
						foreach ($masterArrayKeys as $dateKey) {
							$dateArray = &$this->master_array[$dateKey];
							$dateArrayKeys = array_keys($dateArray);
							foreach ($dateArrayKeys as $timeKey) {
								$arrayOfEvents = &$dateArray[$timeKey];
								$eventKeys = array_keys($arrayOfEvents);
								foreach ($eventKeys as $eventKey) {
									$event = &$arrayOfEvents[$eventKey];
									$this->eventArray[$dateKey.'_'.$event->getType().'_'.$event->getUid().'_'.$event->getStart()->format('%Y%m%d%H%M')] = &$event;
									$starttime = new tx_cal_date();
									$starttime->copy($event->getStart());
									$endtime = new tx_cal_date();
									$endtime->copy($event->getEnd());
									if($timeKey=='-1'){
										$endtime->addSeconds(1); // needed to let allday events show up
									}
									$j = new tx_cal_date();
									$j->copy($starttime);
									$j->setHour(0);
									$j->setMinute(0);
									$j->setSecond(0);
									for ($j;$j->before($endtime); $j->addSeconds(60 * 60 * 24)) {
										$view_array[$j->format('%Y%m%d')]['0000'][count($view_array[$j->format('%Y%m%d')]['0000'])] = $dateKey.'_'.$event->getType().'_'.$event->getUid().'_'.$event->getStart()->format('%Y%m%d%H%M');
									}
								}
							}
						}
					}
					$this->viewarray = &$view_array;
				}
	
				$monthTemplate = str_replace('###MONTH_TITLE###',$month_title,$monthTemplate);
		
				if ($type == 'small') {
					$langtype = '%a';
					$typeSize = 2;
				}
				elseif ($type == 'medium') {
					$langtype = '%a';
				}
				elseif ($type == 'large') {
					$langtype = '%A';
				}
				$dateOfWeek = Date_Calc::beginOfWeek(15,$fake_getdate_time->getMonth(),$fake_getdate_time->getYear());
				$start_day = new tx_cal_date($dateOfWeek.'000000');
				if($weekStartDay=='Sunday'){
					$start_day = $start_day->getPrevDay();
				}
	
				// backwardscompatibility with old templates
				if(!empty($corner)) {
					$weekday_loop .= str_replace('###ADDITIONAL_CLASSES###',$this->conf['view.']['month.']['monthCornerStyle'],$corner);
				} else {
					$weekday_loop .= sprintf($weeknum, $this->conf['view.']['month.']['monthCornerStyle'], '');
				}
	
				for ($i = 0; $i < 7; $i ++) {
					$weekday = $start_day->format($langtype);
					$weekdayLong = $start_day->format('%A');
					if($typeSize){
						$weekday = $this->cs_convert->substr(tx_cal_functions::getCharset(),$weekday,0,$typeSize);
					}
					$start_day->addSeconds(86400);
					
					$additionalClasses = trim(sprintf($this->conf['view.']['month.']['monthDayOfWeekStyle'],$start_day->format('%w')));
					$markerArray = array(
						'###WEEKDAY###' => $weekday,
						'###WEEKDAY_LONG###' => $weekdayLong,
						'###ADDITIONAL_CLASSES###' => ' '.$additionalClasses,
						'###CLASSES###'=> (!empty($additionalClasses) ? ' class="'.$additionalClasses.'" ' : ''),
					);
					$weekday_loop .= strtr($loop_wd,$markerArray);
				}
				$weekday_loop .= $endweek;
				
				$dateOfWeek = Date_Calc::beginOfWeek(1,$fake_getdate_time->getMonth(),$fake_getdate_time->getYear());
				$start_day = new tx_cal_date($dateOfWeek.'000000');
				$start_day->setTZbyID('UTC');
				if($weekStartDay=='Sunday'){
					$start_day = $start_day->getPrevDay();
				}
		
				$i = 0;
				$whole_month = TRUE;
				$isAllowedToCreateEvent = $this->rightsObj->isAllowedToCreateEvent();
	
	
				/*
				 * Holds number of already printed weeks
				 */
				$shownNumberOfWeeks = 0; 
				/*
				 * Holds the week of the last printed day
				 */
				$lastDisplayedWeek = 0; 
				/*
				 * false if current processed week is earlier than the current one
				 */
				$isCurrentWeekOrLater = false; 
	
	
				$createOffset = intval($this->conf['rights.']['create.']['event.']['timeOffset']) * 60;
				
				do {
					$daylink = new tx_cal_date();
					$daylink->copy($start_day);
	
					$formatedGetdate = $daylink->format('%Y%m%d');
					
					$startWeekTime = tx_cal_calendar::calculateStartWeekTime($this->controller->getDateTimeObject);
					$endWeekTime = tx_cal_calendar::calculateEndWeekTime($this->controller->getDateTimeObject);
					$isCurrentWeek = false;
					$isSelectedWeek = false;
					if ($formatedGetdate>=$startWeekTime->format('%Y%m%d') && $formatedGetdate<=$endWeekTime->format('%Y%m%d')) {
						$isSelectedWeek = true;
					}
					if ($start_day->format('%U') == $today->format('%U')) {
						$isCurrentWeek = true;
					}
					/* 
					 * Check if week is the past
					 */
					if ($formatedGetdate>=$startWeekTime->format('%Y%m%d') ) {
						$isCurrentWeekOrLater = true;
					}
	
					if ($i == 0 && !empty($weeknum)){
						$start_day->addSeconds(86400);
						$num = $numPlain = $start_day->format('%U');
						$hasEvent = false;
						
						/*
						 * increase count of displayed weeks when needed
						 */  
						if (($isCurrentWeekOrLater)&&($lastDisplayedWeek!=$num)){
							$lastDisplayedWeek = $num;
							$shownNumberOfWeeks++;
						}						
						
						$start_day->subtractSeconds(86400);
						for($j = 0; $j < 7; $j++){
							if(is_array($this->viewarray[$start_day->format('%Y%m%d')]) || $isAllowedToCreateEvent){
								$hasEvent = true;
								break;
							}
							$start_day->addSeconds(86400);
						}
						$start_day->copy($daylink);
						
						/*
						 * create row only if it's at least the first one to be shown
						 */ 
						if ($shownNumberOfWeeks>0) {
							
							$weekLinkViewTarget = $this->conf['view.']['weekLinkTarget'];
							if(($this->rightsObj->isViewEnabled($weekLinkViewTarget) || $this->conf['view.'][$weekLinkViewTarget.'.'][$weekLinkViewTarget.'ViewPid']) && $hasEvent){
								$this->initLocalCObject();
								$this->local_cObj->setCurrentVal($num);
								$this->local_cObj->data['view'] = $weekLinkViewTarget;
								$this->controller->getParametersForTyposcriptLink($this->local_cObj->data, array ('getdate' => $formatedGetdate, 'view' => $weekLinkViewTarget,  $this->pointerName => NULL), $this->conf['cache'], $this->conf['clear_anyway'], $this->conf['view.'][$weekLinkViewTarget.'.'][$weekLinkViewTarget.'ViewPid']);
								$num  = $this->local_cObj->cObjGetSingle($this->conf['view.'][$weekLinkViewTarget.'.'][$weekLinkViewTarget.'ViewLink'],$this->conf['view.'][$weekLinkViewTarget.'.'][$weekLinkViewTarget.'ViewLink.']);
							}
		
							$className = array();
							if ($isSelectedWeek && !empty($this->conf['view.']['month.']['monthSelectedWeekStyle'])) {
								$className[] = $this->conf['view.']['month.']['monthSelectedWeekStyle'];
							}
							if ($isCurrentWeek && !empty($this->conf['view.']['month.']['monthCurrentWeekStyle'])) {
								$className[] = $this->conf['view.']['month.']['monthCurrentWeekStyle'];
							}
							if ($hasEvent && !empty($this->conf['view.']['month.']['monthWeekWithEventStyle'])) {
								$className[] = $this->conf['view.']['month.']['monthWeekWithEventStyle'];
							}
		
							$weekClasses = trim(implode(' ',$className));
							$markerArray = array (
								'###ADDITIONAL_CLASSES###' => ($weekClasses ? ' '.$weekClasses : ''),
								'###CLASSES###' => ($weekClasses ? ' class="'.$weekClasses.'" ' : ''),
								'###WEEKNUM###' => $num,
								'###WEEKNUM_PLAIN###' => $numPlain,
							);
							$middle .= strtr($startweek,$markerArray);
							// we do this sprintf all only for backwards compatibility with old templates
							$middle .= strtr(sprintf($weeknum,$markerArray['###ADDITIONAL_CLASSES###'],$num),$markerArray);
						}
					}
					$i ++;
					$switch = array ('###ALLDAY###' => '');
					$check_month = $start_day->getMonth();
					
					$switch['###LINK###'] = $this->getCreateEventLink('month','',$start_day,$createOffset,$isAllowedToCreateEvent,'','',$this->conf['view.']['day.']['dayStart']);
					
					$style = array();
					
					$dayLinkViewTarget = $this->conf['view.']['dayLinkTarget'];			
					if(($this->rightsObj->isViewEnabled($dayLinkViewTarget) || $this->conf['view.'][$dayLinkViewTarget.'.'][$dayLinkViewTarget.'ViewPid']) && ($this->viewarray[$formatedGetdate] || $isAllowedToCreateEvent)){
						$this->initLocalCObject();
						$this->local_cObj->setCurrentVal($start_day->getDay());
						$this->local_cObj->data['view'] = $dayLinkViewTarget;
						$this->controller->getParametersForTyposcriptLink($this->local_cObj->data, array ('getdate' => $formatedGetdate, 'view' => $dayLinkViewTarget,  $this->pointerName => NULL), $this->conf['cache'], $this->conf['clear_anyway'], $this->conf['view.'][$dayLinkViewTarget.'.'][$dayLinkViewTarget.'ViewPid']);
						$switch['###LINK###'] .= $this->local_cObj->cObjGetSingle($this->conf['view.'][$dayLinkViewTarget.'.'][$dayLinkViewTarget.'ViewLink'],$this->conf['view.'][$dayLinkViewTarget.'.'][$dayLinkViewTarget.'ViewLink.']);
						$switch['###LINK###'] = $this->cObj->stdWrap($switch['###LINK###'], $this->conf['view.']['month.'][$type.'Link_stdWrap.']);
					}else{
						$switch['###LINK###'] .= $start_day->getDay();
					}
					// add a css class if the current day has a event - regardless if linked or not
					if($this->viewarray[$formatedGetdate]) {
						$style[] = $this->conf['view.']['month.']['eventDayStyle'];
					}
					$style[] = $this->conf['view.']['month.']['month'.ucfirst($type).'Style'];				

					/*
					 * render days only if they are in a week thats not in the past
					 */
					if ($shownNumberOfWeeks>0) {
						/*	
						 * Don't add special style for days in another month 
							if ($check_month != $minical_month) {
								$style[] = $this->conf['view.']['month.']['monthOffStyle'];
							}
						*/
						if ($start_day->format('%w')==0 || $start_day->format('%w')==6) {
							$style[] = $this->conf['view.']['month.']['monthDayWeekendStyle'];
						}
						if ($isSelectedWeek) {
							$style[] = $this->conf['view.']['month.']['monthDaySelectedWeekStyle'];
						}
						if ($formatedGetdate == $this->conf['getdate']) {
							$style[] = $this->conf['view.']['month.']['monthSelectedStyle'];
						}
						if ($isCurrentWeek) {
							$style[] = $this->conf['view.']['month.']['monthDayCurrentWeekStyle'];
						}
						if ($formatedGetdate == $today->format('%Y%m%d')) {
							$style[] = $this->conf['view.']['month.']['monthTodayStyle'];
						}
						if ($this->conf['view.']['month.']['monthDayOfWeekStyle']) {
							$style[] = sprintf($this->conf['view.']['month.']['monthDayOfWeekStyle'],$start_day->format('%w')); 
						}
						
						//clean up empty styles (code beautify)
						foreach($style as $key => $classname) {
							if($classname == '') {
								unset($style[$key]);
							}
						}
						$classesDay = implode(' ',$style);
						$markerArray = array(
							'###STYLE###' => $classesDay,
							'###ADDITIONAL_CLASSES###' => ($classesDay ? ' '.$classesDay : ''),
							'###CLASSES###' => ($classesDay ? ' class="'.$classesDay.'" ' : ''),
						);
						$temp = strtr($t_month,$markerArray);
						$wraped = array();
						
						if ($this->viewarray[$formatedGetdate] && preg_match('!\###EVENT\###!is',$t_month)) {
							foreach ($this->viewarray[$formatedGetdate] as $cal_time => $event_times) {
								foreach ($event_times as $uid => $eventId) {
									if ($type == 'large'){
										$switch['###EVENT###'] .= $this->eventArray[$eventId]->renderEventForMonth();
									} else if ($type == 'medium') {
										$switch['###EVENT###'] .= $this->eventArray[$eventId]->renderEventForYear();
									} else if ($type == 'small') {
										$switch['###EVENT###'] .= $this->eventArray[$eventId]->renderEventForMiniMonth();
									}
								}
							}
						}
		
						$switch['###EVENT###'] = (isset ($switch['###EVENT###'])) ? $switch['###EVENT###'] : '';
						$switch['###ALLDAY###'] = (isset ($switch['###ALLDAY###'])) ? $switch['###ALLDAY###'] : '';
						
			            // Adds hook for processing of extra month day markers
						if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_cal_controller']['extraMonthDayMarkerHook'])) {
							foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_cal_controller']['extraMonthDayMarkerHook'] as $_classRef) {
								$_procObj = & t3lib_div::getUserObj($_classRef);
								if(is_object($_procObj) && method_exists($_procObj,'extraMonthDayMarkerProcessor')) {
									$switch = $_procObj->extraMonthDayMarkerProcessor($this,$daylink,$switch,$type);
								}
							}
						}
					}
			        
					$middle .= tx_cal_functions::substituteMarkerArrayNotCached($temp, $switch, array(), $wraped);
					
							
					$start_day->addSeconds(86400); // 60 * 60 *24 -> strtotime('+1 day', $start_day);
					if ($i == 7) {
						$i = 0;
						$middle .= $endweek;
						if ($shownNumberOfWeeks>=$numOfWeeks) {
							$whole_month = FALSE;
						}
					}
				} while ($whole_month == TRUE);
		
				$rems['###LOOPWEEKDAY###'] = $weekday_loop;
				$rems['###LOOPMONTHWEEKS###'] = $middle;
				$rems['###LOOPMONTHWEEKS_DAYS###'] = '';
				$rems['###LOOPWEEK_NUMS###'] = '';
				$rems['###CORNER###'] = '';
				$monthTemplate = tx_cal_functions::substituteMarkerArrayNotCached($monthTemplate, array (), $rems, array ());
				$page = tx_cal_functions::substituteMarkerArrayNotCached($page, array(), array ('###MONTH_TEMPLATE###'=>$monthTemplate), array ());
			}
			
			$listTemplate = $this->cObj->getSubpart($page, '###LIST###');
			if($listTemplate!=''){
				$tx_cal_listview = &t3lib_div::makeInstanceService('cal_view', 'list', 'list');
				$starttime = gmmktime(0,0,0,$this_month,1,$this_year);
				$endtime = gmmktime(0,0,0,$this_month+1,1,$this_year);
				$rems['###LIST###'] = $tx_cal_listview->drawList($this->master_array,$listTemplate,$starttime,$endtime);
			}
	
			$return = tx_cal_functions::substituteMarkerArrayNotCached($page, array (), $rems, array ());
	
			if($this->rightsObj->isViewEnabled($viewTarget) || $this->conf['view.'][$viewTarget.'.'][$viewTarget.'ViewPid']){
				$this->initLocalCObject();
				$this->local_cObj->setCurrentVal($month_title);
				$this->local_cObj->data['view'] = $viewTarget;
				$this->controller->getParametersForTyposcriptLink($this->local_cObj->data, array ('getdate' => $month_date, 'view' => $viewTarget, $this->pointerName => NULL), $this->conf['cache'], $this->conf['clear_anyway'], $this->conf['view.'][$viewTarget.'.'][$viewTarget.'ViewPid']);
				$month_link  = $this->local_cObj->cObjGetSingle($this->conf['view.'][$viewTarget.'.'][$viewTarget.'ViewLink'],$this->conf['view.'][$viewTarget.'.'][$viewTarget.'ViewLink.']);
			}else{
				$month_link = $month_title;
			}
	
			$return = str_replace('###MONTH_LINK###', $month_link, $return);
				
		} else {
		
			$return = parent::_draw_month($page, $offset, $type);
			 
		} 

		return $return;
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cal_weekpreview/view/class.tx_cal_weekpreview.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cal_weekpreview/view/class.tx_cal_weekpreview.php']);
}
?>
