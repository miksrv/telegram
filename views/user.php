<?php
/**
 * user item list template
 * 
 * @package    TestWork
 * @subpackage Includes
 * @category   user
 * @author     Misha (Mik™) <miksoft.tm@gmail.com> <miksrv.ru>
 */
?>                      <li id="chat<?= $item['user_id'] ?>" onclick="App.Func.GetChat(<?= $item['user_id'] ?>)">
                            <div class="avatar">
                                <?php if ( ! empty($item['user_avatar'])): ?>
                                <img src="/loads/<?= $item['user_avatar'] ?>" alt="" />
                                <?php endif; ?>
                            </div>
                            <div class="container">
                                <div class="header">
                                    <strong><?= $item['user_first_name'] ?><?= $item['user_favorite'] ? '<span class="favorite active"><i class="fas fa-star"></i></span>' : '' ?></strong>
                                    <span class="date"><?= $loader->chat::formatdate($item['item_date']); ?></span>
                                </div>
                                <div class="message"><?= $item['item_answer'] ? '<b>Вы:</b> ' : '' ?><?= $loader->chat::cutoff($item['item_text']); ?><?= ! $item['item_viewed'] ? '<span class="new-message" id="new' . $item['user_id'] . '"></span>' : '' ?></div>
                            </div>
                        </li>