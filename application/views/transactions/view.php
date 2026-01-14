<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h5><?php echo $this->lang->line('Transaction Details') ?> </h5><?php echo '<a href="' . base_url() . 'transactions/print_t?id=' . $trans['id'] . '" class="btn btn-info btn-xs"  title="Print"><span class="fa fa-print"></span></a>'; ?>
            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                    <li><a data-action="close"><i class="ft-x"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content">
            <hr>
            <div class="card-body">
                <div class="row">

                    <hr>
                    <div class="col-md-6">
                        <address>
                            <?php $loc = location($trans['loc']);
                            echo '<strong>' . $loc['cname'] . '</strong><br>' .
                                $loc['address'] . '<br>' . $loc['city'] . ', ' . $loc['region'] . '<br>' . $loc['country'] . ' -  ' . $loc['postbox'] . '<br> ' . $this->lang->line('Phone') . ': ' . $loc['phone'] . '<br>  ' . $this->lang->line('Email') . ': ' . $loc['email'];
                            ?>


                        </address>
                    </div>
                    <div class="col-md-6 text-right">
                        <address>
                            <?php echo '<strong>' . $trans['payer'] . '</strong><br>' .
                                $cdata['address'] . '<br>' . $cdata['city'] . '<br>' . $this->lang->line('Phone') . ': ' . $cdata['phone'] . '<br>  ' . $this->lang->line('Email') . ': ' . $cdata['email']; ?>
                        </address>
                    </div>

                </div>
                <hr>
                <div class="row">


                    <?php 
                    // Format for original format tracking (check if user originally entered Nepali or English)
                    $dateFormat = $trans['date_format'] ?? 'english';
                    
                    echo '<div class="col-md-6">
                    <p>' . $this->lang->line('Debit') . ' : ' . amountExchange($trans['debit'], 0, $this->aauth->get_user()->loc) . ' </p><p>' . $this->lang->line('Credit') . ' : ' . amountExchange($trans['credit'], 0, $this->aauth->get_user()->loc) . ' </p><p>' . $this->lang->line('Type') . ' : ' . $trans['type'] . '</p>
                </div>

                <div class="col-md-6 text-right">
                    <p>' . $this->lang->line('Date') . ' : 
                        <span class="date-display" data-raw="' . $trans['date'] . '" data-format="' . $dateFormat . '">
                            ' . dateformat($trans['date']) . '
                        </span>
                        <span class="english-date" style="display:none; color:#666; font-size:0.9em;"></span>
                    </p>
                    <p>' . $this->lang->line('Transaction') . ' ID : ' . prefix(5) . $trans['id'] . '</p>
                    <p>' . $this->lang->line('Category') . ' : ' . $trans['cat'] . '</p>
            </div><div class="col-md-12 "><hr>
                    <p>' . $this->lang->line('Note') . ' : ' . $trans['note'] . '</p>
            </div></div>'; ?>'

                </div>

            </div>
        </div>
    </div>
</div>