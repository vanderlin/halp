<?php 
class PaginationPresenter extends Illuminate\Pagination\Presenter {

    public function getPageLinkWrapper($url, $page, $rel = null)
    {
        $rel = is_null($rel) ? '' : ' rel="'.$rel.'"';

        return '<li><a href="'.$url.'"'.$rel.'>'.$page.'</a></li>';
    }

    public function getDisabledTextWrapper($text)
    {
        return '<li class="disabled"><span>'.$text.'</span></li>';
    }

    public function getActivePageWrapper($text)
    {
        return '<li class="active"><span>'.$text.'</span></li>';
    }

    protected function getPageSlider()
    {
        $window = 2;

        // If the current page is very close to the beginning of the page range, we will
        // just render the beginning of the page range, followed by the last 2 of the
        // links in this list, since we will not have room to create a full slider.
        if ($this->currentPage <= $window)
        {
            $ending = $this->getFinish();

            return $this->getPageRange(1, $window + 2).$ending;
        }

        // If the current page is close to the ending of the page range we will just get
        // this first couple pages, followed by a larger window of these ending pages
        // since we're too close to the end of the list to create a full on slider.
        elseif ($this->currentPage >= $this->lastPage - $window)
        {
            $start = $this->lastPage;

            $content = $this->getPageRange($start, $this->lastPage);

            return $this->getStart().$content;
        }

        // If we have enough room on both sides of the current page to build a slider we
        // will surround it with both the beginning and ending caps, with this window
        // of pages in the middle providing a Google style sliding paginator setup.
        else
        {
            $content = $this->getAdjacentRange();

            return $this->getStart().$content.$this->getFinish();
        }
    }
}