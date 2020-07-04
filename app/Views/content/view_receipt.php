<?php $this->extend('layouts/app'); ?>
<?php $this->section('content'); ?>
<div class="row">
	<div class="col-md-12 container">
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">View Receipt Info</h6>
			</div>
			<div class="card-body">
					<div class="row">
						<div class="form-group col-4">
							<label for="">Receipt No.</label>
							<input type="text" class="form-control" value="<?=$receipt->receiptId?>" disabled >
						</div>
						<div class="form-group col-4">
							<label for="">Ref No: </label>
							<input type="text" class="form-control" value="<?=$receipt->refNo?>" disabled>
						</div>
						<div class="form-group col-4">
							<label for="">Account Name: </label>
							<input type="text" class="form-control" value="<?=$receipt->accountName?>" disabled>
						</div>
						<div class="form-group col-4">
							<label for="">Narration: </label>
							<input type="text" class="form-control" value="<?=$receipt->narration?>" disabled>
						</div>
						<div class="form-group col-4">
							<label for="">Date: </label>
							<input type="date" class="form-control" value="<?=$receipt->date?>" disabled>
						</div>
						<div class="form-group col-4">
							<label for="">Ref Date: </label>
							<input type="date" class="form-control" value="<?=$receipt->refDate?>" disabled>
						</div>
						<div class="form-group col-4">
							<label for="">Prepared by: </label>
							<input type="text" class="form-control" value="<?=$receipt->receivedFrom?>" disabled>
						</div>
						<hr>
						<div class="form-group col-4">
							<label for="">Description: </label>
							<input type="text" class="form-control" value="<?=$receipt->description?>" disabled>
						</div>
						<div class="form-group col-4">
							<label for="">Currency: </label>
							<input type="text" class="form-control" value="<?=$receipt->currency?>" disabled>
						</div>
						<div class="form-group col-4">
							<label for="">Received by: </label>
							<input type="text" class="form-control" value="<?=$receipt->receivedBy?>" disabled>
						</div>
                        <div class="col-12">
                            <div class="form-group col-12">
                                <form action="<?=base_url('receipts/generate/'.$receipt->receiptId)?>" method="post">
                                <label for="">Amount In Words: </label>
                                <input type="text" class="form-control" id="amountInWords" name="words" ><br>
                                    <input type="submit" class="btn btn-success" value="Download PDF"/>
                                </form>
                            </div>
                        </div>
					</div>
				</form>
			</div>
		</div>
	</div>
    <script>
        function numberToEnglish(n, custom_join_character) {

            var string = n.toString(),
                units, tens, scales, start, end, chunks, chunksLen, chunk, ints, i, word, words;

            var and = custom_join_character || 'and';

            /* Is number zero? */
            if (parseInt(string) === 0) {
                return 'zero';
            }

            /* Array of units as words */
            units = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];

            /* Array of tens as words */
            tens = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

            /* Array of scales as words */
            scales = ['', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion', 'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quatttuor-decillion', 'quindecillion', 'sexdecillion', 'septen-decillion', 'octodecillion', 'novemdecillion', 'vigintillion', 'centillion'];

            /* Split user arguemnt into 3 digit chunks from right to left */
            start = string.length;
            chunks = [];
            while (start > 0) {
                end = start;
                chunks.push(string.slice((start = Math.max(0, start - 3)), end));
            }

            /* Check if function has enough scale words to be able to stringify the user argument */
            chunksLen = chunks.length;
            if (chunksLen > scales.length) {
                return '';
            }

            /* Stringify each integer in each chunk */
            words = [];
            for (i = 0; i < chunksLen; i++) {

                chunk = parseInt(chunks[i]);

                if (chunk) {

                    /* Split chunk into array of individual integers */
                    ints = chunks[i].split('').reverse().map(parseFloat);

                    /* If tens integer is 1, i.e. 10, then add 10 to units integer */
                    if (ints[1] === 1) {
                        ints[0] += 10;
                    }

                    /* Add scale word if chunk is not zero and array item exists */
                    if ((word = scales[i])) {
                        words.push(word);
                    }

                    /* Add unit word if array item exists */
                    if ((word = units[ints[0]])) {
                        words.push(word);
                    }

                    /* Add tens word if array item exists */
                    if ((word = tens[ints[1]])) {
                        words.push(word);
                    }

                    /* Add 'and' string after units or tens integer if: */
                    if (ints[0] || ints[1]) {

                        /* Chunk has a hundreds integer or chunk is the first of multiple chunks */
                        if (ints[2] || !i && chunksLen) {
                            words.push(and);
                        }

                    }

                    /* Add hundreds word if array item exists */
                    if ((word = units[ints[2]])) {
                        words.push(word + ' hundred');
                    }

                }

            }

            return words.reverse().join(' ');

        }
    </script>
    <script>
        //alert(numberToEnglish(123456789));
        var num=numberToEnglish(<?=$receipt->amount?>);
        //alert(parseInt(n[1]));
        $("#amountInWords").val(num);
    </script>
</div>
<?php $this->endsection();?>
